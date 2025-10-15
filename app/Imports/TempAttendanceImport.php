<?php

namespace App\Imports;

use App\Models\Attendance;
use App\Models\Employee;
use App\Services\TempAttendanceService;
use Carbon\Carbon;
use DateTime;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;

class TempAttendanceImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        DB::beginTransaction();
        $data = $rows->skip(1); // skip header
        // $data = $rows->skip(3);
        $data->map(function ($row) {
            $unixTimestamp = ($row[2] - 25569) * 86400;
            $date_time = new DateTime("@$unixTimestamp");
            $row->date = $date_time->format('Y-m-d');
            $row->time = $date_time->format('H:i:s');
            $row->employee_no = $row[1];
            $row->type = $row[3];
            return $row;
        });
        $employee_group = $data->groupBy('employee_no');
        foreach ($employee_group as $employee_no => $employee_date) {
            $employee = Employee::where('employee_no', $employee_no)->first();
            if (!$employee)
                continue;
            $date_grouped = $employee_date->groupBy('date');
            foreach ($date_grouped as $date => $date_data) {
                $date_data = $date_data->map(function ($item) {
                    $item->carbon_time = Carbon::parse($item->time);
                    return $item;
                })->sortBy('carbon_time');
                $attendance_data['employee_id'] = $employee->id;
                $attendance_data['date'] = $date;
                $attendance_data['start'] = $date_data->first()->time;;
                if ($date_data->count() > 1) {
                    $attendance_data['end'] = $date_data->last()->time;
                }
                $attendance = resolve(TempAttendanceService::class)->store($attendance_data);
                $attendance_data = [];
            }
        }
        DB::commit();
        return true;
    }
}
