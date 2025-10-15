<?php

namespace App\Observers;

use App\Helpers\Helpers;
use App\Models\Shift;
use App\Services\ModelRecordService;
use Carbon\Carbon;

class ShiftObserver
{

    /**
     * Handle the Shift "creating" event.
     */
    public function creating(Shift $shift)
    {
        $shift->daily_hours = Helpers::calculateDailyHours($shift->start_time, $shift->end_time);
    }

    /**
     * Handle the Shift "updating" event.
     */
    public function updating(Shift $shift)
    {
        $shift->daily_hours = Helpers::calculateDailyHours($shift->start_time, $shift->end_time);
    }

    /**
     * Handle the Shift "created" event.
     */
    public function created(Shift $shift): void
    {
        resolve(ModelRecordService::class)->store($shift, auth()->id(), 'create');
    }

    /**
     * Handle the Shift "updated" event.
     */
    public function updated(Shift $shift): void
    {
        resolve(ModelRecordService::class)->store($shift, auth()->id(), 'update');
    }

    /**
     * Handle the Shift "deleted" event.
     */
    public function deleted(Shift $shift): void
    {
        resolve(ModelRecordService::class)->store($shift, auth()->id(), 'delete');
    }

    /**
     * Handle the Shift "restored" event.
     */
    public function restored(Shift $shift): void
    {
        resolve(ModelRecordService::class)->store($shift, auth()->id(), 'restore');
    }

    /**
     * Handle the Shift "force deleted" event.
     */
    public function forceDeleted(Shift $shift): void
    {
        //
    }
}
