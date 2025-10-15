<?php

namespace App\Observers;

use App\Models\Bill;
use App\Services\ModelRecordService;

class BillObserver
{
    /**
     * Handle the Bill "created" event.
     */
    public function created(Bill $bill): void
    {
        resolve(ModelRecordService::class)->store($bill, auth()->id(), 'create');
    }

    /**
     * Handle the Bill "updated" event.
     */
    public function updated(Bill $bill): void
    {
        resolve(ModelRecordService::class)->store($bill, auth()->id(), 'update');
    }

    /**
     * Handle the Bill "deleted" event.
     */
    public function deleted(Bill $bill): void
    {
        resolve(ModelRecordService::class)->store($bill, auth()->id(), 'delete');
    }

    /**
     * Handle the Bill "restored" event.
     */
    public function restored(Bill $bill): void
    {
        resolve(ModelRecordService::class)->store($bill, auth()->id(), 'restore');
    }

    /**
     * Handle the Bill "force deleted" event.
     */
    public function forceDeleted(Bill $bill): void
    {
        //
    }
}
