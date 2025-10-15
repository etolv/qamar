<?php

namespace App\Services;

use App\Enums\CashFlowStatusEnum;
use App\Enums\CashFlowTypeEnum;
use App\Enums\ItemTypeEnum;
use App\Enums\PaymentStatusEnum;
use App\Enums\PaymentTypeEnum;
use App\Enums\ServiceStatusEnum;
use App\Enums\StatusEnum;
use App\Events\StoreLoyaltyPoints;
use App\Jobs\StoreLoyaltyPointsJob;
use App\Jobs\StoreTransactionJob;
use App\Models\Coupon;
use App\Models\Employee;
use App\Models\Order;
use App\Models\Rate;
use App\Traits\PaymentTrait;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

/**
 * Class OrderService.
 */
class OrderService
{
    public function __construct(
        private ProductService $productService,
        private ServiceService $serviceService,
        private StockService $stockService,
        private CouponService $couponService,
        private PaymentService $paymentService,
        private PackageService $packageService,
        private PackageItemService $packageItemService,
        private OrderPackageItemService $orderPackageItemService,
        private OrderServiceService $orderServiceService,
        private OrderStockService $orderStockService,
        private TransferService $transferService,
        private SettingService $settingService,
        private CashFlowService $cashFlowService,
    ) {}

    use PaymentTrait;

    public function all($data = [], $withes = [], $paginated = false, $excluded_ids = [])
    {
        $query = Order::with($withes)->when(isset($data['customer_id']), function ($query) use ($data) {
            $query->where('customer_id', $data['customer_id']);
        })->when(isset($data['status']), function ($query) use ($data) {
            $query->where('status', $data['status']);
        })->when(isset($data['from']), function ($query) use ($data) {
            $query->where('created_at', '>=', $data['from']);
        })->when(isset($data['to']), function ($query) use ($data) {
            $query->where('created_at', '<=', $data['to']);
        })->when(isset($data['search']), function ($query) use ($data) {
            $query->where('id', 'like', "%{$data['status']}%");
        })->latest();
        return $paginated ? $query->paginate(10) : $query->get();
    }

    public function complete($id)
    {
        $order = $this->show($id);
        $order->update(['status' => StatusEnum::COMPLETED->value]);
        $order->orderServices()->where('status', ServiceStatusEnum::PENDING->value)->update(['status' => ServiceStatusEnum::COMPLETED->value]);
        $this->store_order_transfers($order->id);
        return $order;
    }

    public function rate($data, $id)
    {
        $order = $this->show($id);
        if (isset($data['rates'])) {
            foreach ($data['rates'] as $rate) {
                Rate::updateOrCreate([
                    'model_type' => get_class($order),
                    'model_id' => $order->id,
                    'type' => $rate['type']
                ], $rate);
            }
        } elseif (isset($data['rate'])) {
            Rate::updateOrCreate([
                'model_type' => get_class($order),
                'model_id' => $order->id,
                'type' => $data['rate']['type']
            ], $data['rate']);
        }
        return $order;
    }

    public function customerOrderCount()
    {
        return Order::distinct('customer_id')->count('customer_id');
    }

    public function storeApiOrder($data)
    {
        //
    }

    public function store($data)
    {
        DB::beginTransaction();
        $order = Order::create(Arr::except($data, ['stocks', 'services', 'payments']));
        $tax_percentage = $this->settingService->fromKey('tax')?->value ?? 15;
        $total = 0;
        $tax = 0;
        $discount = 0;
        if (isset($data['packages'])) {
            foreach ($data['packages'] as $package) {
                $package_model = $this->packageService->show($package['id']);
                $order_package = $order->orderPackages()->create([
                    'package_id' => $package['id'],
                    'total_price' => $package_model->total
                ]);
                if (isset($package['stocks'])) {
                    foreach ($package['stocks'] as $stock) {
                        $item_model = $this->packageItemService->show($stock['id']);
                        $orderPackageItem = $this->orderStockService->store([
                            'package_item_id' => $item_model->id,
                            'order_package_id' => $order_package->id,
                            'stock_id' => $item_model->item_id,
                            'quantity' => $item_model->quantity,
                            'price' => $item_model->price,
                            'order_id' => $order->id,
                            'type' => ItemTypeEnum::PACKAGE->value
                        ]);
                    }
                }
                if (isset($package['services'])) {
                    foreach ($package['services'] as $service) {
                        $item_model = $this->packageItemService->show($service['id']);
                        for ($i = 0; $i < $item_model->quantity; $i++) {
                            $orderPackageItem = $this->orderServiceService->store([
                                'service_id' => $item_model->item_id,
                                'package_item_id' => $item_model->id,
                                'quantity' => 1,
                                'price' => $item_model->price,
                                'employee_id' => $service['employee'] ?? null,
                                'order_package_id' => $order_package->id,
                                'order_id' => $order->id,
                                'type' => ItemTypeEnum::PACKAGE->value
                            ]);
                        }
                        foreach ($item_model->item->productServices as $productService) {
                            //TODO check product stock in validation
                            $stock = $this->stockService->product_stock(product_id: $productService->product_id);
                            if ($stock) {
                                $order_stock = $this->orderStockService->store([
                                    'stock_id' => $stock->id,
                                    'order_id' => $order->id,
                                    'quantity' => 1,
                                    'price' => 0, // service product price is 0
                                    'order_service_id' => $orderPackageItem->id,
                                    'type' => $productService->required ? ItemTypeEnum::SERVICE->value : ItemTypeEnum::NORMAL->value,
                                ]);
                            }
                        }
                    }
                }
                $total += $package_model->total;  //TODO remove product qty from stocks
                $tax += $package_model->total * ($tax_percentage / 100);
            }
        }
        if (isset($data['stocks'])) {
            foreach ($data['stocks'] as $stock) {
                $product_stock = $this->stockService->show($stock['id']); //TODO check product stock in validation
                $order_stock = $this->orderStockService->store([
                    'stock_id' => $stock['id'],
                    'quantity' => $stock['quantity'],
                    'price' => $product_stock->price,
                    'order_id' => $order->id,
                ]);
                $total += ($product_stock->price * $stock['quantity']);  //TODO remove product qty from stocks
                $tax += ($product_stock->price * $stock['quantity']) * ($tax_percentage / 100);
            }
        }
        if (isset($data['services'])) {
            foreach ($data['services'] as $service) {
                $service_model = $this->serviceService->show($service['id']);
                $session_price = $service['session_price'] ?? 0.00;
                for ($i = 0; $i < $service['quantity']; $i++) {
                    $order_service = $this->orderServiceService->store([
                        'service_id' => $service['id'],
                        'quantity' => 1,
                        'price' => $service_model->price,
                        'employee_id' => $service['employee'] ?? null,
                        'session_count' => $service['session_count'] ?? 1,
                        'due_date' => $service['due_date'] ?? null,
                        'session_price' => $session_price,
                        'order_id' => $order->id,
                    ]);
                }
                $total += (($service_model->price * $service['quantity']) + $session_price);
                $tax += ($service_model->price * $service['quantity']) * ($tax_percentage / 100);
                foreach ($service_model->productServices as $productService) {
                    //TODO check product stock in validation
                    // if ($productService->require) {
                    $stock = $this->stockService->product_stock(product_id: $productService->product_id);
                    if ($stock) {
                        $order_stock = $this->orderStockService->store([
                            'stock_id' => $stock->id,
                            'order_id' => $order->id,
                            'quantity' => 1,
                            'price' => 0, // service product price is 0
                            'order_service_id' => $order_service->id,
                            'type' => $productService->required ? ItemTypeEnum::SERVICE->value : ItemTypeEnum::NORMAL->value,
                        ]);
                    }
                    // }
                }
            }
        }
        if (isset($data['coupon_id'])) { //discount coupon based on coupon services 
            $coupon = $this->couponService->show($data['coupon_id']);
            $coupon_services = $coupon->services->pluck('id')->toArray();
            $coupon_products = $coupon->products->pluck('id')->toArray();
            foreach ($order->orderServices as $orderService) {
                if (!count($coupon_services) || in_array($orderService->service_id, $coupon_services)) {
                    $discount += ($orderService->quantity * $orderService->price * $coupon->discount / 100);
                }
            }
            foreach ($order->orderStocks as $orderStock) {
                if (!count($coupon_products) || in_array($orderStock->stock->product_id, $coupon_products)) {
                    $discount += ($orderStock->quantity * $orderStock->price * $coupon->discount / 100);
                }
            }
        }
        if ($data['is_gift'] && !isset($data['gifter_id'])) { //gift from admin
            $discount = $total;
        }
        if (isset($data['points'])) {
            $points = $order->customer->points;
            $points_to_cash = $this->settingService->fromKey('points_to_cash')?->value ?? 0;
            $cash = $points_to_cash * $points;
            if (($discount + $cash) > $total) {
                $points = ($total - $discount) / $points_to_cash;
                $discount = $total;
                $cash = $points_to_cash * $points;
            } else {
                $discount += $cash;
            }
            $order->payments()->create([
                'type' => PaymentTypeEnum::POINT->value,
                'status' => PaymentStatusEnum::PAID->value,
                'amount' => $cash,
            ]);
            $decremented = resolve(LoyaltyService::class)->decrementPoints($order->customer_id, $points);
        }
        if ($data['tax_included']) {
            $grand_total = $total - $discount;
            $total -= $tax;
        } else {
            $grand_total = $total - $discount + $tax;
        }
        $order->update([
            'total' => $total,
            'tax' => $tax,
            'grand_total' => $grand_total,
            'discount' => $discount
        ]);
        if (isset($data['payments'])) {
            foreach ($data['payments'] as $payment) {
                if (isset($data['left']) && $data['left'] > 0 && $payment['type'] == PaymentTypeEnum::CASH->value) {
                    if ($payment['amount'] > $data['left']) {
                        $payment['amount'] -= $data['left'];
                        $data['left'] = 0;
                    } else {
                        $payment['amount'] = 0;
                        $data['left'] -= $payment['amount'];
                    }
                }
                $payment_model = $order->payments()->create($payment);
                if ($payment_model->status == PaymentStatusEnum::PENDING && $payment_model->type == PaymentTypeEnum::ONLINE) {
                    $online_payment = $this->createPayment($payment_model);
                }
            }
        } else {
            $payment_model = $order->payments()->create([
                'type' => $data['payment_type'],
                'status' => PaymentStatusEnum::PENDING->value,
                'amount' => $grand_total
            ]);
            if ($data['payment_type'] == PaymentTypeEnum::ONLINE->value) {
                $online_payment = $this->createPayment($payment_model);
            }
        }
        if (isset($data['left']) && $data['left'] > 0) {
            $order->payments()->create([
                'type' => PaymentTypeEnum::CASH->value,
                'status' => PaymentStatusEnum::RETURN->value,
                'amount' => $data['left']
            ]);
        }
        $order->load('payments');
        $order->refresh();
        DB::commit();
        StoreLoyaltyPointsJob::dispatch($order->id);
        return $order;
    }

    public function store_order_transfers($order_id)
    {
        DB::beginTransaction();
        $order = $this->show($order_id);
        foreach ($order->orderStocks as $orderStock) {
            if ($orderStock->type == ItemTypeEnum::SERVICE->value) {
                $bill_product = $orderStock->stock->billProduct;
                $product_cost = $bill_product->exchange_price - ($bill_product->tax / $bill_product->convert);
                $employee = $orderService->employee ?? $order->employee;
                $this->transferService->store($orderStock->stock, $employee, ($orderStock->stock->price * $orderStock->quantity), $orderStock->quantity, 'sell');
                $employee_deduct = $this->cashFlowService->store([
                    'flowable_type' => Employee::class,
                    'flowable_id' => $employee->id,
                    'type' => CashFlowTypeEnum::DEDUCT->value,
                    'status' => CashFlowStatusEnum::PENDING->value,
                    'amount' => ($product_cost * $orderStock->quantity),
                    'reason' => 'Order Service #' . $order->id,
                    'due_date' => Carbon::now()->endOfMonth()->format('Y-m-d')
                ]);
            } else {
                $this->transferService->store($orderStock->stock, $orderStock, ($orderStock->price * $orderStock->quantity), $orderStock->quantity, 'sell');
            }
            $this->stockService->withdraw($orderStock->stock_id, $orderStock->quantity);
        }
        StoreTransactionJob::dispatch($order);
        DB::commit();
        return true;
    }

    public function show($id, $withes = [])
    {
        return Order::withTrashed()->with(array_merge($withes, ['stocks', 'services', 'orderStocks.stock.product', 'orderServices.service', 'rates.reason']))->findOrFail($id);
    }

    public function update($data, $id)
    {
        DB::beginTransaction();
        $order = $this->show($id);
        $order->update(Arr::except($data, ['stocks', 'services']));
        $total = 0;
        $order->orderStocks()->delete();
        if (isset($data['stocks'])) {
            foreach ($data['stocks'] as $stock_id) {
                $product_stock = $this->stockService->show($stock_id); //TODO check product stock in validation
                $order->orderStocks()->create([
                    'stock_id' => $stock_id,
                    'price' => $product_stock->price
                ]);
                $total += $product_stock->price; //TODO remove product qty from stocks
            }
        }
        $order->orderServices()->delete();
        foreach ($data['services'] as $service_id) {
            $service = $this->serviceService->show($service_id);
            $order->orderServices()->create([
                'service_id' => $service_id,
                'price' => $service->price
            ]);
            $total += $service->price;
        }
        $order->update(['total' => $total]);
        DB::commit();
        return $order;
    }
}
