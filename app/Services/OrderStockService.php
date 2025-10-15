<?php

namespace App\Services;

use App\Models\OrderStock;

/**
 * Class OrderStockService.
 */
class OrderStockService
{
    public function store($data): OrderStock
    {
        return OrderStock::create($data);
    }
}
