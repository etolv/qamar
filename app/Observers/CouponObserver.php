<?php

namespace App\Observers;

use App\Models\Coupon;
use App\Services\ModelRecordService;

class CouponObserver
{
    /**
     * Handle the Coupon "created" event.
     */
    public function created(Coupon $coupon): void
    {
        resolve(ModelRecordService::class)->store($coupon, auth()->id(), 'create');
    }

    /**
     * Handle the Coupon "updated" event.
     */
    public function updated(Coupon $coupon): void
    {
        resolve(ModelRecordService::class)->store($coupon, auth()->id(), 'update');
    }

    /**
     * Handle the Coupon "deleted" event.
     */
    public function deleted(Coupon $coupon): void
    {
        resolve(ModelRecordService::class)->store($coupon, auth()->id(), 'delete');
    }

    /**
     * Handle the Coupon "restored" event.
     */
    public function restored(Coupon $coupon): void
    {
        resolve(ModelRecordService::class)->store($coupon, auth()->id(), 'restore');
    }

    /**
     * Handle the Coupon "force deleted" event.
     */
    public function forceDeleted(Coupon $coupon): void
    {
        //
    }
}
