<?php

namespace App\Observers;

use App\Models\OrderService;

class OrderServiceObserver
{
    /**
     * Handle the OrderService "created" event.
     */
    public function created(OrderService $orderService): void
    {
        //
    }

    /**
     * Handle the OrderService "updated" event.
     */
    public function updated(OrderService $orderService): void
    {
        //
    }

    /**
     * Handle the OrderService "deleted" event.
     */
    public function deleted(OrderService $orderService): void
    {
        //
    }

    /**
     * Handle the OrderService "restored" event.
     */
    public function restored(OrderService $orderService): void
    {
        //
    }

    /**
     * Handle the OrderService "force deleted" event.
     */
    public function forceDeleted(OrderService $orderService): void
    {
        //
    }
}
