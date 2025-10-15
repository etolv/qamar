<?php

namespace App\Services;

use App\Models\CafeteriaOrderStock;

/**
 * Class CafeteriaOrderStockService.
 */
class CafeteriaOrderStockService
{
    public function store($data): CafeteriaOrderStock
    {
        return CafeteriaOrderStock::create($data);
    }
}
