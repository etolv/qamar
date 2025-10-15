<?php

namespace App\Services;

use App\Models\PackageItem;
use App\Models\Service;
use App\Models\Stock;

/**
 * Class PackageItemService.
 */
class PackageItemService
{
    public function show($id)
    {
        return PackageItem::with([
            'item' => function ($morphTo) {
                $morphTo->morphWith([
                    Stock::class => ['product'],
                    Service::class => ['productServices.product']
                ]);
            }
        ])->find($id);
    }
}
