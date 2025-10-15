<?php

namespace App\Observers;

use App\Models\Stock;
use App\Services\ModelRecordService;

class StockObserver
{
    /**
     * Handle the Stock "created" event.
     */
    public function created(Stock $stock): void
    {
        resolve(ModelRecordService::class)->store($stock, auth()->id(), 'create');
    }

    /**
     * Handle the Stock "updated" event.
     */
    public function updated(Stock $stock): void
    {
        resolve(ModelRecordService::class)->store($stock, auth()->id(), 'update');
    }

    /**
     * Handle the Stock "deleted" event.
     */
    public function deleted(Stock $stock): void
    {
        resolve(ModelRecordService::class)->store($stock, auth()->id(), 'delete');
    }

    /**
     * Handle the Stock "restored" event.
     */
    public function restored(Stock $stock): void
    {
        resolve(ModelRecordService::class)->store($stock, auth()->id(), 'restore');
    }

    /**
     * Handle the Stock "force deleted" event.
     */
    public function forceDeleted(Stock $stock): void
    {
        //
    }
}
