<?php

namespace App\Observers;

use App\Models\CafeteriaOrder;
use App\Services\ModelRecordService;

class CafeteriaOrderObserver
{
    /**
     * Handle the CafeteriaOrder "created" event.
     */
    public function created(CafeteriaOrder $cafeteriaOrder): void
    {
        resolve(ModelRecordService::class)->store($cafeteriaOrder, auth()->id(), 'create');
    }

    /**
     * Handle the CafeteriaOrder "updated" event.
     */
    public function updated(CafeteriaOrder $cafeteriaOrder): void
    {
        resolve(ModelRecordService::class)->store($cafeteriaOrder, auth()->id(), 'update');
    }

    /**
     * Handle the CafeteriaOrder "deleted" event.
     */
    public function deleted(CafeteriaOrder $cafeteriaOrder): void
    {
        resolve(ModelRecordService::class)->store($cafeteriaOrder, auth()->id(), 'delete');
    }

    /**
     * Handle the CafeteriaOrder "restored" event.
     */
    public function restored(CafeteriaOrder $cafeteriaOrder): void
    {
        resolve(ModelRecordService::class)->store($cafeteriaOrder, auth()->id(), 'restore');
    }

    /**
     * Handle the CafeteriaOrder "force deleted" event.
     */
    public function forceDeleted(CafeteriaOrder $cafeteriaOrder): void
    {
        //
    }
}
