<?php

namespace App\Services;

use App\Imports\TempAttendanceImport;
use App\Models\TempAttendance;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Class TempAttendanceService.
 */
class TempAttendanceService
{

    public function __construct(private AttendanceService $attendanceService) {}
    public function import($data)
    {
        Excel::import(new TempAttendanceImport, $data['file']);
        return true;
    }

    public function store($data)
    {
        $data = $this->attendanceService->calculateAttendanceData($data);
        return TempAttendance::updateOrCreate([
            'employee_id' => $data['employee_id'],
            'date' => $data['date'],
        ], $data);
    }

    public function clear()
    {
        return TempAttendance::truncate();
    }
}
