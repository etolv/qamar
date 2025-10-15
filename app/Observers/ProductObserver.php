<?php

namespace App\Observers;

use App\Enums\ModelLogEnum;
use App\Models\ModelRecord;
use App\Models\Product;
use App\Services\ModelRecordService;

class ProductObserver
{
    /**
     * Handle the Product "created" event.
     */
    public function created(Product $product): void
    {
        resolve(ModelRecordService::class)->store($product, auth()->id(), 'create');
    }

    /**
     * Handle the Product "updated" event.
     */
    public function updated(Product $product): void
    {
        resolve(ModelRecordService::class)->store($product, auth()->id(), 'update');
    }

    /**
     * Handle the Product "deleted" event.
     */
    public function deleted(Product $product): void
    {
        resolve(ModelRecordService::class)->store($product, auth()->id(), 'delete');
    }

    /**
     * Handle the Product "restored" event.
     */
    public function restored(Product $product): void
    {
        resolve(ModelRecordService::class)->store($product, auth()->id(), 'restore');
    }

    /**
     * Handle the Product "force deleted" event.
     */
    public function forceDeleted(Product $product): void
    {
        //
    }
}
