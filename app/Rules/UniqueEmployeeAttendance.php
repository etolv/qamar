<?php

namespace App\Rules;

use App\Models\Employee;
use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\Validation\Rule;

class UniqueEmployeeAttendance implements Rule
{
    protected $date;
    protected $employee_name;

    public function __construct($date)
    {
        $this->date = $date;
        $this->employee_name = '';
    }

    public function passes($attribute, $value)
    {
        $date = Carbon::parse($this->date)->format('Y-m-d');
        $employee = Employee::find($value);
        if (!$employee) {
            return false;
        }
        $exists = $employee->attendances()->where('date', $date)->exists();
        if ($exists) {
            $this->employee_name = $employee->user->name;
            return false;
        }
        return true;
    }

    public function message()
    {
        return $this->employee_name . _t(' already has an attendance for this period of time');
    }
}
