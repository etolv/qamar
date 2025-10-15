<?php

namespace App\Observers;

use App\Models\Attendance;
use App\Services\ModelRecordService;
use Carbon\Carbon;

class AttendanceObserver
{
    /**
     * Handle the Attendance "creating" event.
     */
    public function creating(Attendance $attendance)
    {
        // $this->calculateDailyHours($attendance);
    }

    /**
     * Handle the Attendance "updating" event.
     */
    public function updating(Attendance $attendance)
    {
        // $this->calculateDailyHours($attendance);
    }

    /**
     * Handle the Attendance "created" event.
     */
    public function created(Attendance $attendance): void
    {
        resolve(ModelRecordService::class)->store($attendance, auth()->id(), 'create');
    }

    /**
     * Handle the Attendance "updated" event.
     */
    public function updated(Attendance $attendance): void
    {
        resolve(ModelRecordService::class)->store($attendance, auth()->id(), 'update');
    }

    /**
     * Handle the Attendance "deleted" event.
     */
    public function deleted(Attendance $attendance): void
    {
        resolve(ModelRecordService::class)->store($attendance, auth()->id(), 'delete');
    }

    /**
     * Handle the Attendance "restored" event.
     */
    public function restored(Attendance $attendance): void
    {
        resolve(ModelRecordService::class)->store($attendance, auth()->id(), 'restore');
    }

    /**
     * Handle the Attendance "force deleted" event.
     */
    public function forceDeleted(Attendance $attendance): void
    {
        //
    }

    private function calculateDailyHours(Attendance $attendance)
    {
        // Assuming start and end are Carbon instances
        if ($attendance->start && $attendance->end) {
            // Calculate the difference in hours between start and end
            $startWork = Carbon::parse($attendance->start);
            $endWork = Carbon::parse($attendance->end);

            // Calculate the hours difference
            $attendance->total = $endWork->diffInHours($startWork);
        }
    }
}
