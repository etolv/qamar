<?php

namespace App\Services;

use App\Models\OrderPackageItem;

/**
 * Class OrderPackageItemService.
 */
class OrderPackageItemService
{
    public function store($data)
    {
        return OrderPackageItem::create($data);
    }
}
