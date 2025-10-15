<?php

namespace App\Services;

use App\Enums\ModelLogEnum;
use App\Models\ModelRecord;
use Illuminate\Support\Facades\Log;

/**
 * Class ModelRecordService.
 */
class ModelRecordService
{
    public function store($item, $user_id, $type)
    {
        try {
            $type = strtoupper($type);
            return ModelRecord::create([
                'user_id' => $user_id,
                'model_type' => get_class($item),
                'model_id' => $item->id,
                'type' => ModelLogEnum::fromName($type)->value
            ]);
        } catch (\Exception $e) {
            Log::error(print_r($e));
            return false;
        }
    }
}
