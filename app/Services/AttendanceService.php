<?php

namespace App\Services;

use App\Enums\AttendanceStatusEnum;
use App\Enums\VacationStatusEnum;
use App\Helpers\Helpers;
use App\Imports\AttendanceImport;
use App\Models\Attendance;
use App\Models\EmployeeShift;
use App\Models\Shift;
use App\Models\Vacation;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

/**
 * Class AttendanceService.
 */
class AttendanceService extends BaseService
{
    public function import($data)
    {
        Excel::import(new AttendanceImport, $data['file']);
        return true;
    }

    public function store($data)
    {
        $data = $this->calculateAttendanceData($data);
        return Attendance::updateOrCreate([
            'employee_id' => $data['employee_id'],
            'date' => $data['date'],
        ], $data);
    }

    public function update($data, $id)
    {
        $attendance = $this->show($id);
        if (!isset($data['overtime_status']))
            $data = $this->calculateAttendanceData($data);
        $attendance->update($data);
        return $attendance;
    }

    public function calculateAttendanceData(array $data): array
    {
        if (!isset($data['start'])) {
            $data['start'] = null;
        }
        if (!isset($data['end'])) {
            $data['end'] = $data['start'];
        }
        // $data['total'] = round(Carbon::parse($data['start'])->diffInSeconds(Carbon::parse($data['end'])) / 3600, 2);
        $data['on_vacation'] = Vacation::where('employee_id', $data['employee_id'])
            ->where('start_date', '<=', $data['date'])->where('end_date', '>=', $data['date'])
            ->where('status', VacationStatusEnum::APPROVED->value)->exists();

        $employee_shift = EmployeeShift::where('date', $data['date'])->where('employee_id', $data['employee_id'])->first();
        // Shift::whereHas('employeeShifts', function ($query) use ($data) {
        //     $query->where('employee_id', $data['employee_id']);
        // })->where(function ($query) use ($data) {
        //     $query->where('start', '<=', $data['date'])
        //         ->where('end', '>=', $data['date']);
        // })->first();
        $data['total'] = Helpers::calculateDailyHours($data['start'], $data['end']);

        if ($employee_shift) {
            $shift = $employee_shift->shift;
            $data['shift_id'] = $shift->id;
            $data['missing_hours'] = max($shift->daily_hours - $data['total'], 0);
            $data['extra_hours'] = max($data['total'] - $shift->daily_hours, 0);
            $data['is_holiday'] = $shift->holiday->value == Carbon::parse($data['date'])->dayOfWeek;

            if ($data['is_holiday'] || $data['on_vacation']) {
                $data['extra_hours'] = $data['total'];
                $data['missing_hours'] = 0;
            }
            if ($data['on_vacation']) {
                $data['status'] = AttendanceStatusEnum::VACATION->value;
            } elseif ($data['start'] == null && $data['end'] == null) {
                $data['status'] = AttendanceStatusEnum::ABSENT->value;
            } elseif (Carbon::parse($shift->start_time) < Carbon::parse($data['start'])) {
                $data['status'] = AttendanceStatusEnum::LATE->value;
            } elseif (Carbon::parse($shift->end_time) > Carbon::parse($data['end'])) {
                $data['status'] = AttendanceStatusEnum::LEAVE_EARLY->value;
            } else {
                $data['status'] = AttendanceStatusEnum::NORMAL->value;
            }
        }
        return $data;
    }
}
