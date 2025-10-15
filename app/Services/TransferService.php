<?php

namespace App\Services;

use App\Enums\TransferTypeEnum;
use App\Models\Transfer;
use Illuminate\Support\Facades\DB;

/**
 * Class TransferService.
 */
class TransferService
{
    public function store($from, $to, $amount = 0, $quantity = 0, $type = 1, $note = null)
    {
        DB::beginTransaction();
        $transfer = Transfer::create([
            'from_type' => get_class($from),
            'from_id' => $from->id,
            'to_type' => get_class($to),
            'to_id' => $to->id,
            'quantity' => $quantity,
            'total' => $amount,
            'note' => $note,
            'type' => TransferTypeEnum::fromName(strtoupper($type))->value
        ]);
        DB::commit();
        return $transfer;
    }
}
