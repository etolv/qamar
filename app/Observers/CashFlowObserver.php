<?php

namespace App\Observers;

use App\Models\CashFlow;
use App\Services\ModelRecordService;

class CashFlowObserver
{
    /**
     * Handle the CashFlow "created" event.
     */
    public function created(CashFlow $cashFlow): void
    {
        resolve(ModelRecordService::class)->store($cashFlow, auth()->id(), 'create');
    }

    /**
     * Handle the CashFlow "updated" event.
     */
    public function updated(CashFlow $cashFlow): void
    {
        resolve(ModelRecordService::class)->store($cashFlow, auth()->id(), 'update');
    }

    /**
     * Handle the CashFlow "deleted" event.
     */
    public function deleted(CashFlow $cashFlow): void
    {
        resolve(ModelRecordService::class)->store($cashFlow, auth()->id(), 'delete');
    }

    /**
     * Handle the CashFlow "restored" event.
     */
    public function restored(CashFlow $cashFlow): void
    {
        resolve(ModelRecordService::class)->store($cashFlow, auth()->id(), 'restore');
    }

    /**
     * Handle the CashFlow "force deleted" event.
     */
    public function forceDeleted(CashFlow $cashFlow): void
    {
        //
    }
}
