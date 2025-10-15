<?php

namespace App\Observers;

use App\Models\OrderStock;

class OrderServiceStock
{
    /**
     * Handle the OrderStock "created" event.
     */
    public function created(OrderStock $orderStock): void
    {
        //
    }

    /**
     * Handle the OrderStock "updated" event.
     */
    public function updated(OrderStock $orderStock): void
    {
        //
    }

    /**
     * Handle the OrderStock "deleted" event.
     */
    public function deleted(OrderStock $orderStock): void
    {
        //
    }

    /**
     * Handle the OrderStock "restored" event.
     */
    public function restored(OrderStock $orderStock): void
    {
        //
    }

    /**
     * Handle the OrderStock "force deleted" event.
     */
    public function forceDeleted(OrderStock $orderStock): void
    {
        //
    }
}
