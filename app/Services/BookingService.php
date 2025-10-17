<?php

namespace App\Services;

use App\Enums\ItemTypeEnum;
use App\Enums\PaymentStatusEnum;
use App\Enums\PaymentTypeEnum;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

/**
 * Class BookingService.
 */
class BookingService
{

    public function __construct(
        private ProductService $productService,
        private ServiceService $serviceService,
        private StockService $stockService,
        private CouponService $couponService,
        private SettingService $settingService,
    ) {}

    public function all($data = [], $withes = [], $paginated = false)
    {
        $query = Booking::with($withes)->when(isset($data['customer_id']), function ($query) use ($data) {
            $query->where('customer_id', $data['customer_id']);
        })->when(isset($data['status']), function ($query) use ($data) {
            $query->where('status', $data['status']);
        })->when(isset($data['statuses']) && is_array($data['statuses']), function ($query) use ($data) {
            $query->whereIn('status', $data['statuses']);
        })->when(isset($data['from']), function ($query) use ($data) {
            $query->where('created_at', '>=', $data['from']);
        })->when(isset($data['to']), function ($query) use ($data) {
            $query->where('created_at', '<=', $data['to']);
        })->when(isset($data['search']), function ($query) use ($data) {
            $query->where('id', 'like', "{$data['search']}")
                ->orWhereRelation('customer.user', 'name', 'like', "%{$data['search']}%")
                ->orWhereRelation('customer.user', 'phone', 'like', "%{$data['search']}%");
        })->latest();
        return $paginated ? $query->paginate(10) : $query->get();
    }

    public function fetch($request)
    {
        $data = DataTables::eloquent(
            Booking::withTrashed()->with([
                'customer.user' => function ($query) {
                    $query->withTrashed();
                },
                'employee.user' => function ($query) {
                    $query->withTrashed();
                }
            ])->when($request->customer_id, function ($query) use ($request) {
                $query->where('customer_id', $request->customer_id);
            })->when($request->status, function ($query) use ($request) {
                $query->where('status', $request->status);
            })->when($request->is_mobile, function ($query) use ($request) {
                $query->where('is_mobile', $request->is_mobile);
            })->when($request->date, function ($query) use ($request) {
                $query->where('created_at', '>=', $request->date);
            })->latest()
        )->addColumn('customer_image', function ($item) {
            return $item->customer->user->getFirstMediaUrl('profile');
        })->addColumn('employee_image', function ($item) {
            return $item->employee?->user?->getFirstMediaUrl('profile');
        })->editColumn('status', function ($item) {
            return _t($item->status?->name ?? '');
        })->editColumn('payment_status', function ($item) {
            return _t($item->payment_status?->name ?? '');
        })->editColumn('created_at', function ($item) {
            return Carbon::parse($item->created_at)->format('Y-m-d H:i');
        })->editColumn('date', function ($item) {
            return Carbon::parse($item->date)->format('Y-m-d H:i');
        })->toJson();
        return $data;
    }

    public function store($data)
    {
        // dd($data);
        DB::beginTransaction();
        $booking = Booking::create(Arr::except($data, ['stocks', 'services']));
        $tax_percentage = $this->settingService->fromKey('tax')?->value ?? 15;
        $total = 0;
        $tax = 0;

        if (isset($data['stocks'])) {
            foreach ($data['stocks'] as $stock) {
                $product_stock = $this->stockService->show($stock['id']); //TODO check product stock in validation
                $booking->bookingProducts()->create([
                    'stock_id' => $stock['id'],
                    'quantity' => $stock['quantity'],
                    'price' => $product_stock->price
                ]);
                $total += ($product_stock->price * $stock['quantity']);
            }
        }

        foreach ($data['services'] as $service) {
            $service_model = $this->serviceService->show($service['id']);
            $session_price = $service['session_price'] ?? 0.00;
            $booking_service = $booking->bookingServices()->create([
              'service_id' => $service['id'],
              'quantity' => $service['quantity'],
              'employee_id' => $service['employee'] ?? null,
              'price' => $service_model->price,
            ]);
            $total += (($service_model->price * $service['quantity']) + $session_price);
            foreach ($service_model->productServices as $productService) {
                $stock = $this->stockService->product_stock(product_id: $productService->product_id);
                if ($stock) {
                    $booking->bookingProducts()->create([
                        'stock_id' => $stock->id,
                        'quantity' => $service['quantity'],
                        'price' => 0, // service product price is 0
                        'booking_service_id' => $booking_service->id,
                        'type' => $productService->required ? ItemTypeEnum::SERVICE->value : ItemTypeEnum::NORMAL->value,
                    ]);
                }
            }
        }

        $discount = 0;
        if (isset($data['coupon_id'])) {
             $coupon = $this->couponService->show($data['coupon_id']);
          $discount = $coupon->is_percentage
            ? $total * $coupon->discount / 100
            : $coupon->discount;
        }

        $subtotal_after_discount = $total - $discount;
        $tax = $subtotal_after_discount * ($tax_percentage / 100);
        $grand_total = $subtotal_after_discount + $tax;

        $booking->update([
          'total' => $total,
          'grand_total' => $grand_total,
          'discount' => $discount,
          'tax' => $tax
        ]);
        // $booking->payments()->create([
        //     'amount' => $grand_total,
        //     'type' => PaymentTypeEnum::CASH->value,
        //     'status' => PaymentStatusEnum::PENDING->value
        // ]);
        DB::commit();
        return $booking;
    }

    public function show($id)
    {
        return Booking::with('products', 'services', 'rates', 'customer.user', 'addressModel','coupon')->find($id);
    }

    public function update($data, $id)
    {
        DB::beginTransaction();
        $booking = $this->show($id);
        $booking->update(Arr::except($data, ['stocks', 'services']));
        $total = 0;
        $booking->bookingProducts()->delete();
        $booking->bookingServices()->delete();

        $tax_percentage = $this->settingService->fromKey('tax')?->value ?? 15;

        if (isset($data['stocks'])) {
            foreach ($data['stocks'] as $stock) {
              $stockId = is_array($stock) ? $stock['id'] : $stock;
              $quantity = is_array($stock) ? ($stock['quantity'] ?? 1) : 1;
              $product_stock = $this->stockService->show($stockId);
              $booking->bookingProducts()->create([
                'stock_id' => $stockId,
                'quantity' => $quantity,
                'price' => $product_stock->price
              ]);
              $total += ($product_stock->price * $quantity);
            }
        }

        if (isset($data['services'])) {
          foreach ($data['services'] as $service) {
            $serviceId = is_array($service) ? $service['id'] : $service;
            $quantity = is_array($service) ? ($service['quantity'] ?? 1) : 1;
            $employeeId = is_array($service) ? ($service['employee'] ?? null) : null;
            $service_model = $this->serviceService->show($serviceId);
            $booking_service = $booking->bookingServices()->create([
              'service_id' => $serviceId,
              'quantity' => $quantity,
              'employee_id' => $employeeId,
              'price' => $service_model->price
            ]);
            $total += ($service_model->price * $quantity);
            foreach ($service_model->productServices as $productService) {
              $stock = $this->stockService->product_stock(product_id: $productService->product_id);
              if ($stock) {
                $booking->bookingProducts()->create([
                  'stock_id' => $stock->id,
                  'quantity' => $quantity,
                  'price' => 0,
                  'booking_service_id' => $booking_service->id,
                  'type' => $productService->required
                    ? ItemTypeEnum::SERVICE->value
                    : ItemTypeEnum::NORMAL->value,
                ]);
              }
          }
      }
    }

    $discount = 0;
    if (!empty($booking->coupon_id) && $booking->coupon) {
      $discount = $booking->coupon->is_percentage
        ? ($total * $booking->coupon->discount / 100)
        : $booking->coupon->discount;
    }

    $subtotal_after_discount = $total - $discount;
    $tax = $subtotal_after_discount * ($tax_percentage / 100);
    $grand_total = $subtotal_after_discount + $tax;

    $booking->update([
      'total' => $total,
      'discount' => $discount,
      'tax' => $tax,
      'grand_total' => $grand_total
    ]);

    DB::commit();
    return $booking;
  }

}
