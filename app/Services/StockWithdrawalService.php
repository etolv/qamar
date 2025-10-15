<?php

namespace App\Services;

use App\Enums\StockWithdrawalTypeEnum;
use App\Enums\TransferTypeEnum;
use App\Jobs\StoreTransactionJob;
use App\Models\StockWithdrawal;
use Illuminate\Support\Facades\DB;

/**
 * Class StockWithdrawalService.
 */
class StockWithdrawalService
{

    public function __construct(protected TransferService $transferService, protected StockService $stockService, protected SettingService $settingService) {}
    public function store($data)
    {
        DB::beginTransaction();
        $stock_withdrawal = null;
        $tax_percentage = $this->settingService->fromKey('tax')?->value ?? 15;
        if (isset($data['stocks']) && is_array($data['stocks'])) {
            foreach ($data['stocks'] as $stock) {
                $stock['tax'] = $stock['quantity'] * $stock['price'] * ($tax_percentage / 100);
                $stock['price'] = $stock['price'] - ($stock['price'] * ($tax_percentage / 100));
                $stock_model = $this->stockService->show($stock['stock_id']);
                $stock_withdrawal = StockWithdrawal::create(array_merge($data, $stock));
                $transfer = $this->transferService->store($stock_model, $stock_withdrawal, $stock['price'], $stock['quantity'], StockWithdrawalTypeEnum::fromValue($data['type'])->name);
                StoreTransactionJob::dispatch($stock_withdrawal);
            }
        } else if (isset($data['stock_id'])) {
            $stock_model = $this->stockService->show($data['stock_id']);
            $data['tax'] = $data['quantity'] * $data['price'] * ($tax_percentage / 100);
            $data['price'] = $data['price'] - ($data['price'] * ($tax_percentage / 100));
            $stock_withdrawal = StockWithdrawal::create($data);
            $transfer = $this->transferService->store($stock_model, $stock_withdrawal, $data['price'], $data['quantity'], StockWithdrawalTypeEnum::fromValue($data['type'])->name);
            StoreTransactionJob::dispatch($stock_withdrawal);
        }
        DB::commit();
        return $stock_withdrawal;
    }

    public function show($id)
    {
        return StockWithdrawal::with('stock.product', 'employee.user')->find($id);
    }
}
