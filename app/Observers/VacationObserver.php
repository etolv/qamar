<?php

namespace App\Observers;

use App\Enums\AttendanceStatusEnum;
use App\Enums\VacationStatusEnum;
use App\Enums\VacationTypeEnum;
use App\Models\Attendance;
use App\Models\Vacation;
use App\Services\ModelRecordService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class VacationObserver
{
    /**
     * Handle the Vacation "created" event.
     */
    public function created(Vacation $vacation): void
    {
        if (($vacation->type == VacationTypeEnum::ANNUAL && $vacation->status == VacationStatusEnum::APPROVED)
            || in_array($vacation->type, [VacationTypeEnum::ROUNDED, VacationTypeEnum::CASHED])
        ) {
            $vacation->employee()->update([
                'used_vacation_days' => DB::raw('used_vacation_days + ' . $vacation->days),
                'remaining_vacation_days' => DB::raw('remaining_vacation_days - ' . $vacation->days),
            ]);
        }
        if ($vacation->status == VacationStatusEnum::APPROVED && $vacation->type != VacationTypeEnum::UNPAID) {
            $attendances = Attendance::where('employee_id', $vacation->employee_id)
                ->whereBetween('date', [$vacation->start_date, $vacation->end_date])->get();
            foreach ($attendances as $attendance) {
                $attendance->update(['status' => AttendanceStatusEnum::VACATION->value]);
            }
        }
        resolve(ModelRecordService::class)->store($vacation, auth()->id(), 'create');
    }

    /**
     * Handle the Vacation "updated" event.
     */
    public function updated(Vacation $vacation): void
    {
        if ($vacation->isDirty('status')) {
            if ($vacation->status == VacationStatusEnum::APPROVED) {
                $vacation->updateQuietly(['approved_at' => Carbon::now()]);
            }
            if ($vacation->type == VacationTypeEnum::ANNUAL) {
                if ($vacation->status == VacationStatusEnum::APPROVED) {
                    $vacation->employee->update([
                        'used_vacation_days' => DB::raw('used_vacation_days + ' . $vacation->days),
                        'remaining_vacation_days' => DB::raw('remaining_vacation_days - ' . $vacation->days),
                    ]);
                } elseif (in_array($vacation->status, [VacationStatusEnum::DECLINED, VacationStatusEnum::CANCELED]) && $vacation->getOriginal('status') == VacationStatusEnum::APPROVED) {
                    $vacation->employee()->update([
                        'used_vacation_days' => DB::raw('used_vacation_days - ' . $vacation->days),
                        'remaining_vacation_days' => DB::raw('remaining_vacation_days + ' . $vacation->days)
                    ]);
                }
            }
            if ($vacation->status == VacationStatusEnum::APPROVED && $vacation->type != VacationTypeEnum::UNPAID) {
                $attendances = Attendance::where('employee_id', $vacation->employee_id)
                    ->whereBetween('date', [$vacation->start_date, $vacation->end_date])->get();
                foreach ($attendances as $attendance) {
                    $attendance->update(['status' => AttendanceStatusEnum::VACATION->value]);
                }
            }
        }
        resolve(ModelRecordService::class)->store($vacation, auth()->id(), 'update');
    }

    /**
     * Handle the Vacation "deleted" event.
     */
    public function deleted(Vacation $vacation): void
    {
        if ($vacation->type == VacationTypeEnum::ANNUAL->value) {
            if ($vacation->status == VacationStatusEnum::APPROVED->value) {
                $vacation->employee->update([
                    'used_vacation_days' => DB::raw('used_vacation_days - ' . $vacation->days),
                    'remaining_vacation_days' => DB::raw('remaining_vacation_days + ' . $vacation->days),
                ]);
            }
        }
        resolve(ModelRecordService::class)->store($vacation, auth()->id(), 'delete');
    }

    /**
     * Handle the Vacation "restored" event.
     */
    public function restored(Vacation $vacation): void
    {
        resolve(ModelRecordService::class)->store($vacation, auth()->id(), 'restore');
    }

    /**
     * Handle the Vacation "force deleted" event.
     */
    public function forceDeleted(Vacation $vacation): void
    {
        //
    }
}
