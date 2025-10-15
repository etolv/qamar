<?php

namespace App\Observers;

use App\Models\Booking;
use App\Services\ModelRecordService;

class BookingObserver
{
    /**
     * Handle the Booking "created" event.
     */
    public function created(Booking $booking): void
    {
        resolve(ModelRecordService::class)->store($booking, auth()->id(), 'create');
    }

    /**
     * Handle the Booking "updated" event.
     */
    public function updated(Booking $booking): void
    {
        resolve(ModelRecordService::class)->store($booking, auth()->id(), 'update');
    }

    /**
     * Handle the Booking "deleted" event.
     */
    public function deleted(Booking $booking): void
    {
        resolve(ModelRecordService::class)->store($booking, auth()->id(), 'delete');
    }

    /**
     * Handle the Booking "restored" event.
     */
    public function restored(Booking $booking): void
    {
        resolve(ModelRecordService::class)->store($booking, auth()->id(), 'restore');
    }

    /**
     * Handle the Booking "force deleted" event.
     */
    public function forceDeleted(Booking $booking): void
    {
        //
    }
}
