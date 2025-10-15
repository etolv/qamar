<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\EmployeeShift;
use Illuminate\Support\Facades\DB;

/**
 * Class EmployeeShiftService.
 */
class EmployeeShiftService extends BaseService
{
    public function store($data)
    {
        DB::beginTransaction();
        if (isset($data['employee_shifts']) && is_array($data['employee_shifts'])) {
            foreach ($data['employee_shifts'] as $employee_shift) {
                $employeeShift = EmployeeShift::create($employee_shift);
            }
        } else {
            $employeeShift = EmployeeShift::create($data);
        }
        DB::commit();
        return $employeeShift;
    }
}
