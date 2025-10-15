<?php

namespace App\Services;

use App\Models\CafeteriaOrderService;

/**
 * Class CafeteriaOrderServiceService.
 */
class CafeteriaOrderServiceService
{
    public function store($data): CafeteriaOrderService
    {
        return CafeteriaOrderService::create($data);
    }
}
