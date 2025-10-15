<?php

namespace App\Services;

use App\Enums\PaymentStatusEnum;
use App\Enums\PaymentTypeEnum;
use App\Enums\TaxTypeEnum;
use App\Jobs\StoreTransactionJob;
use App\Models\Bill;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

/**
 * Class BillService.
 */
class BillService
{

    public function __construct(
        protected SettingService $settingService,
        protected StockService $stockService,
        protected ProductService $productService,
        protected BillProductService $billProductService,
        protected PaymentService $paymentService,
        protected TransferService $transferService
    ) {}


    public function all($data = [], $withes = [], $paginate = true)
    {
        $query = Bill::when(isset($data['search']), function ($query) use ($data) {
            $query->where('identifier', 'like', "%{$data['search']}%");
        })->when(isset($data['from']), function ($query) use ($data) {
            $query->where('created_at', '>=', $data['from']);
        })->when(isset($data['to']), function ($query) use ($data) {
            $query->where('created_at', '<=', $data['to']);
        })->when(isset($data['bill_type_id']), function ($query) use ($data) {
            $query->where('bill_type_id', $data['bill_type_id']);
        })->when(isset($data['type']), function ($query) use ($data) {
            $query->where('type', $data['type']);
        })->when(isset($data['supplier_id']), function ($query) use ($data) {
            $query->where(function ($query) use ($data) {
                $query->where('supplier_id', $data['supplier_id'])
                    ->orWhereRelation('supplier', 'supplier_id', $data['supplier_id']);
            });
        })->with($withes);
        return $paginate ? $query->paginate() : $query->get();
    }
    public function store($data): Bill
    {
        DB::beginTransaction();
        $profit_percentage = $this->settingService->fromKey('profit_percentage')?->value ?? 0;
        $bill = Bill::create(Arr::only($data, ['supplier_id', 'paid', 'identifier', 'bill_type_id', 'received', 'receiving_date', 'type', 'department', 'term', 'tax_type']));
        $total = 0;
        $tax = 0;
        $tax_percentage = $this->settingService->fromKey('tax')?->value ?? 15;
        if (isset($data['products']) && count($data['products'])) {

            foreach ($data['products'] as $index => &$product) {
                if ($product['tax_type'] == TaxTypeEnum::TAXED->value) {
                    $product['tax'] = $product['purchase_price'] * ($tax_percentage / 100);
                    $tax += ($product['quantity'] * $product['purchase_price']) * ($tax_percentage / 100);
                }
                $product_model = $this->productService->show($product['product_id']);
                $product['profit_percentage'] = $profit_percentage;
                $product['bill_id'] = $bill->id;
                $bill_product = $this->billProductService->store($product);
                $product['bill_product_id'] = $bill_product->id;
                $product['barcode'] = $product_model->sku;
                $product['unit_id'] = $bill_product->retail_unit_id;
                $product['department'] = $bill->department;
                $total += $product['quantity'] * $product['purchase_price'];
                // $total += $product_price = $product['quantity'] * $product['purchase_price'];
                // $item_price = $product_price / ($product['quantity'] * $product['convert']);
                // $product['price'] = $item_price + (($item_price * $profit_percentage) / 100);
                $product['price'] = $product['sell_price'];
                $product['quantity'] = $product['quantity'] * $product['convert'];
                if ($data['received']) {
                    $stock = $this->stockService->store($product);
                    $transfer = $this->transferService->store($bill_product, $stock, $product['purchase_price'], $product['quantity'], 'buy');
                }
            }
        } else {
            $total = $data['total'];
            if ($data['tax_type'] == TaxTypeEnum::TAXED->value) {
                $tax += $data['total'] * ($tax_percentage / 100);
            }
        }
        $bill->update([
            'total' => $total - $tax, // tax considered included even if it was 0
            'tax' => $tax,
            'grand_total' => $total
        ]);
        if ($data['paid']) {
            $payment = $this->paymentService->store($bill, [
                'type' => $data['payment_type'],
                'status' => PaymentStatusEnum::PAID->value,
                'amount' => $data['paid'],
                'card_id' => isset($data['card_id']) ? $data['card_id'] : null
            ]);
        }
        if (isset($data['file'])) {
            $bill->addMedia($data['file'])->toMediaCollection('file');
        }
        StoreTransactionJob::dispatch($bill);
        DB::commit();
        return $bill;
    }

    public function received($bill_id)
    {
        DB::beginTransaction();
        $bill = $this->show($bill_id);
        $bill->update(['received' => true, 'receiving_date' => Carbon::now()]);
        foreach ($bill->billProducts as $bill_product) {
            $bill_product->price = $bill_product->sell_price;
            $purchased_quantity = $bill_product->quantity;
            $bill_product->quantity = $purchased_quantity * $bill_product->convert;
            $bill_product->unit_id = $bill_product->retail_unit_id;
            $stock = $this->stockService->store($bill_product->toArray());
            $transfer = $this->transferService->store($bill_product, $stock, $bill_product->purchase_price, $purchased_quantity, 'buy');
        }
        DB::commit();
        return $bill;
    }

    public function store_payment($bill_id, $data)
    {
        $bill = $this->show($bill_id);
        $payment =  $this->paymentService->store($bill, $data);
        StoreTransactionJob::dispatch($payment);
        return $payment;
    }

    public function show($id)
    {
        return Bill::with('billProducts', 'supplier', 'payments')->find($id);
    }

    public function destroy($bill_id)
    {
        DB::beginTransaction();
        $bill = $this->show($bill_id);
        if ($bill->received) {
            foreach ($bill->billProducts as $billProduct) {
                $transfer = $billProduct->transfer;
                $stock = $transfer->to;
                dd($stock->transfersTo);
                if ($transfer->quantity != $stock->quantity) {
                    DB::rollBack();
                    return false;
                }
                $stock->forceDelete();
                $transfer->forceDelete();
                $billProduct->forceDelete();
            }
        }
        $deleted = $bill->forceDelete();
        DB::commit();
        return $deleted;
    }
}
