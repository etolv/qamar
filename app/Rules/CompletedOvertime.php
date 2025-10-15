<?php

namespace App\Rules;

use App\Enums\OverTimeStatusEnum;
use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\Validation\Rule;

class CompletedOvertime implements Rule
{

    public function __construct(protected $month, protected $employee_name = '') {}
    public function passes($attribute, $value)
    {
        // Calculate the number of vacation days requested
        $start = Carbon::createFromDate(null, $this->month, 1)->startOfMonth()->format('Y-m-d');
        $end = Carbon::createFromDate(null, $this->month, 1)->endOfMonth()->format('Y-m-d');
        $employee = Employee::find($value);

        if (!$employee) {
            return false;
        }
        $not_completed_over_time = Attendance::where('employee_id', $employee->id)
            ->whereBetween('date', [$start, $end])->where('extra_hours', '>', 0)
            ->where('overtime_status', OverTimeStatusEnum::PENDING->value)->exists();
        if ($not_completed_over_time) {
            $this->employee_name = $employee->user->name;
            return false;
        }

        return true;
    }

    public function message()
    {
        return $this->employee_name . _t(' have unclosed overtime.');
    }
}
