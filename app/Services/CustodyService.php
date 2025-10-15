<?php

namespace App\Services;

use App\Enums\CustodyStatusEnum;
use App\Enums\StockWithdrawalTypeEnum;
use App\Models\Custody;
use App\Models\Stock;
use Illuminate\Support\Facades\DB;

/**
 * Class CustodyService.
 */
class CustodyService
{

    public function __construct(
        protected StockService $stockService,
        protected TransferService $transferService,
        protected StockWithdrawalService $stockWithdrawalService
    ) {}
    public function store($data)
    {
        DB::beginTransaction();
        $custody = Custody::create($data);
        $stock = $this->stockService->withdraw($custody->stock_id, $custody->quantity);
        $transfer = $this->transferService->store($stock, $custody, $custody->price, $custody->quantity, 'custody');
        DB::commit();
        return $custody;
    }

    public function show($id): Custody
    {
        return Custody::find($id);
    }

    public function return($data, $id)
    {
        DB::beginTransaction();
        $custody = $this->show($id);
        $stock = $this->stockService->insert($custody->stock_id, $data['quantity']);
        $transfer = $this->transferService->store($stock, $custody, $custody->price, $data['quantity'], 'return', $data['reason']);
        if ($data['quantity'] == $custody->quantity) {
            $custody->update(['status' => CustodyStatusEnum::RETURNED->value]);
        } else {
            $custody->update(['quantity' => $custody->quantity - $data['quantity']]);
        }
        DB::commit();
        return $custody;
    }

    public function waste($data, $id)
    {
        DB::beginTransaction();
        $returned_custody = $this->return($data, $id);
        $stock = $this->stockService->withdraw($returned_custody->stock_id, $returned_custody->quantity);
        $wasted_stock = $this->stockWithdrawalService->store([
            'stock_id' => $returned_custody->stock_id,
            'type' => StockWithdrawalTypeEnum::WASTE->value,
            'quantity' => $returned_custody->quantity,
            'price' => $returned_custody->price,
            'reason' => $data['reason']
        ]);
        $transfer = $this->transferService->store($stock, $wasted_stock, $returned_custody->price, $returned_custody->quantity, 'return', $data['reason']);
        $returned_custody->update(['status' => CustodyStatusEnum::WASTED->value]);
        DB::commit();
        return $returned_custody;
    }
}
