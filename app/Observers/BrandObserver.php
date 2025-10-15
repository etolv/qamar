<?php

namespace App\Observers;

use App\Enums\ModelLogEnum;
use App\Models\Brand;
use App\Models\ModelRecord;
use App\Services\ModelRecordService;

class BrandObserver
{
    /**
     * Handle the Brand "created" event.
     */
    public function created(Brand $brand): void
    {
        resolve(ModelRecordService::class)->store($brand, auth()->id(), 'create');
    }

    /**
     * Handle the Brand "updated" event.
     */
    public function updated(Brand $brand): void
    {
        resolve(ModelRecordService::class)->store($brand, auth()->id(), 'update');
    }

    /**
     * Handle the Brand "deleted" event.
     */
    public function deleted(Brand $brand): void
    {
        resolve(ModelRecordService::class)->store($brand, auth()->id(), 'delete');
    }

    /**
     * Handle the Brand "restored" event.
     */
    public function restored(Brand $brand): void
    {
        resolve(ModelRecordService::class)->store($brand, auth()->id(), 'restore');
    }

    /**
     * Handle the Brand "force deleted" event.
     */
    public function forceDeleted(Brand $brand): void
    {
        //
    }
}
