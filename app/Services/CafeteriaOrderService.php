<?php

namespace App\Services;

use App\Enums\PaymentStatusEnum;
use App\Enums\PaymentTypeEnum;
use App\Enums\StatusEnum;
use App\Models\CafeteriaOrder;
use App\Models\Order;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

/**
 * Class CafeteriaOrderService.
 */
class CafeteriaOrderService
{

    public function __construct(
        private StockService $stockService,
        private CafeteriaOrderStockService $cafeteriaOrderStockService,
        private TransferService $transferService,
        private SettingService $settingService,
        private ServiceService $serviceService,
        private CafeteriaOrderServiceService $cafeteriaOrderServiceService
    ) {}

    public function all($data = [], $withes = [], $paginated = false)
    {
        $query = CafeteriaOrder::with($withes)->when(isset($data['status']), function ($query) use ($data) {
            $query->where('status', $data['status']);
        })->when(isset($data['from']), function ($query) use ($data) {
            $query->where('created_at', '>=', $data['from']);
        })->when(isset($data['to']), function ($query) use ($data) {
            $query->where('created_at', '<=', $data['to']);
        })->latest();
        return $paginated ? $query->paginate(10) : $query->get();
    }

    public function show($id, $withes = [])
    {
        return CafeteriaOrder::with($withes)->find($id);
    }
    public function store($data): CafeteriaOrder
    {
        DB::beginTransaction();
        $order = CafeteriaOrder::create(Arr::except($data, ['payments', 'stocks']));
        $tax_percentage = $this->settingService->fromKey('tax')?->value ?? 15;
        $total = 0;
        $tax = 0;
        $discount = 0;
        if (isset($data['stocks'])) {
            foreach ($data['stocks'] as $stock) {
                $product_stock = $this->stockService->show($stock['id']); //TODO check product stock in validation
                $order_stock = $this->cafeteriaOrderStockService->store([
                    'stock_id' => $stock['id'],
                    'quantity' => $stock['quantity'],
                    'price' => $product_stock->price,
                    'cafeteria_order_id' => $order->id,
                ]);
                $this->transferService->store($order_stock->stock, $order_stock, ($order_stock->price * $order_stock->quantity), $order_stock->quantity, 'sell');
                $this->stockService->withdraw($order_stock->stock_id, $order_stock->quantity);
                $total += ($product_stock->price * $stock['quantity']);  //TODO remove product qty from stocks
                $tax += ($product_stock->price * $stock['quantity']) * ($tax_percentage / 100);
            }
        }
        if (isset($data['services'])) {
            foreach ($data['services'] as $service) {
                $service_model = $this->serviceService->show($service['id']);
                $order_service = $this->cafeteriaOrderServiceService->store([
                    'service_id' => $service['id'],
                    'quantity' => $service['quantity'],
                    'price' => $service_model->price,
                    'cafeteria_order_id' => $order->id,
                ]);
                $total += ($service_model->price * $service['quantity']);
                $tax += ($service_model->price * $service['quantity']) * ($tax_percentage / 100);
            }
        }
        if ($data['tax_included']) {
            $grand_total = $total - $discount;
        } else {
            $grand_total = $total - $discount + $tax;
        }
        $order->update([
            'total' => $total,
            'tax' => $tax,
            'grand_total' => $grand_total,
            'discount' => $discount
        ]);
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
            $order->payments()->create($payment);
        }
        if (isset($data['left']) && $data['left'] > 0) {
            $order->payments()->create([
                'type' => PaymentTypeEnum::CASH->value,
                'status' => PaymentStatusEnum::RETURN->value,
                'amount' => $data['left']
            ]);
        }
        DB::commit();
        return $order;
    }

    public function cancel($id)
    {
        $order = CafeteriaOrder::find($id);
        foreach ($order->orderStocks as $orderStock) {
            $this->transferService->store($orderStock, $orderStock->stock, ($orderStock->price * $orderStock->quantity), $orderStock->quantity, 'return');
            $this->stockService->insert($orderStock->stock_id, $orderStock->quantity);
        }
        foreach ($order->payments as $payment) {
            $payment->update(['status' => PaymentStatusEnum::RETURN->value]);
        }
        $order->update(['payment_status' => PaymentStatusEnum::RETURN->value]);
        $order->update(['status' => StatusEnum::CANCELED->value]);
        return $order;
    }
}
