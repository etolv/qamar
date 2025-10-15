<?php

namespace App\Services;

use App\Enums\PaymentStatusEnum;
use App\Enums\PaymentTypeEnum;
use App\Http\Requests\ReturnCustodyRequest;
use App\Jobs\StoreTransactionJob;
use App\Models\BillReturn;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

/**
 * Class BillReturnService.
 */
class BillReturnService
{

    public function __construct(
        private StockService $stockService,
        private TransferService $transferService,
        private PaymentService $paymentService,
        private SettingService $settingService
    ) {}

    public function all($data = [], $withes = [], $paginated = false)
    {
        $query = BillReturn::when(isset($data['from']), function ($query) use ($data) {
            $query->where('created_at', '>=', $data['from']);
        })->when(isset($data['to']), function ($query) use ($data) {
            $query->where('created_at', '<=', $data['to']);
        })->when(isset($data['bill_type_id']), function ($query) use ($data) {
            $query->where('bill_type_id', $data['bill_type_id']);
        })->when(isset($data['bill_id']), function ($query) use ($data) {
            $query->where('bill_id', $data['bill_id']);
        })->when(isset($data['supplier_id']), function ($query) use ($data) {
            $query->where('supplier_id', $data['supplier_id']);
        })->with($withes);
        return $paginated ? $query->paginate() : $query->get();
    }
    public function store($data)
    {
        DB::beginTransaction();
        $total = 0;
        $tax = 0;
        $tax_percentage = $this->settingService->fromKey('tax')?->value ?? 15;
        $bill_return = BillReturn::create(Arr::only($data, ['supplier_id', 'bill_id', 'reason']));
        foreach ($data['stocks'] as $stock) {
            $stock_model = $this->stockService->show($stock['stock_id']);
            if ($stock['quantity'] > $stock_model->quantity) {
                DB::rollBack();
                return false;
            }
            $bill_return_stock = $bill_return->billReturnStocks()->create([
                'stock_id' => $stock['stock_id'],
                'price' => $stock['return_price'],
                'quantity' => $stock['quantity'],
            ]);
            $total += ($stock['return_price'] * $stock['quantity']);
            $stock_withdraw = $this->stockService->withdraw($stock['stock_id'], $stock['quantity']);
            $transfer = $this->transferService->store($stock_model, $bill_return_stock, $stock['return_price'], $stock['quantity'], 'return');
        }
        $tax = $total * ($tax_percentage / 100);
        $bill_return->update([
            'total' => $total - $tax,
            'tax' => $tax,
            'grand_total' => $total
        ]);
        if ($bill_return->bill) {
            $bill = $bill_return->bill;
            $left = $bill->grand_total - $bill->payments->sum('amount');
            if ($left >= $total) {
                $payment = $this->paymentService->store($bill, [
                    'type' => PaymentTypeEnum::CASH,
                    'status' => PaymentStatusEnum::PAID->value,
                    'amount' => $total
                ]);
                $total = 0;
                StoreTransactionJob::dispatch($payment);
            }
        }
        if ($total) {
            $payment = $this->paymentService->store($bill_return, [
                'type' => PaymentTypeEnum::CASH,
                'status' => PaymentStatusEnum::PAID->value,
                'amount' => $total,
            ]);
        }
        StoreTransactionJob::dispatch($bill_return);
        DB::commit();
        return $bill_return;
    }
}
