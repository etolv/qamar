<?php

namespace App\Observers;

use App\Jobs\ProcessModelObserver;
use App\Models\Order;
use App\Services\LoyaltyService;
use App\Services\ModelRecordService;
use App\Services\OrderService;

class OrderObserver
{
    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        ProcessModelObserver::dispatch($order, 'create', auth()->id());
        resolve(ModelRecordService::class)->store($order, auth()->id(), 'create');
    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        resolve(ModelRecordService::class)->store($order, auth()->id(), 'update');
    }

    /**
     * Handle the Order "deleted" event.
     */
    public function deleted(Order $order): void
    {
        resolve(ModelRecordService::class)->store($order, auth()->id(), 'delete');
    }

    /**
     * Handle the Order "restored" event.
     */
    public function restored(Order $order): void
    {
        resolve(ModelRecordService::class)->store($order, auth()->id(), 'restore');
    }

    /**
     * Handle the Order "force deleted" event.
     */
    public function forceDeleted(Order $order): void
    {
        //
    }
}
