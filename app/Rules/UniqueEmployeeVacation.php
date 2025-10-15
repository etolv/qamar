<?php

namespace App\Rules;

use App\Enums\VacationStatusEnum;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;

class UniqueEmployeeVacation implements Rule
{

    protected $startDate;
    protected $endDate;
    protected $employee_name;
    protected $hours;

    public function __construct($startDate, $endDate, $from_hour, $to_hour)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        if (!$endDate) {
            $this->endDate = $startDate;
        }
        $this->hours = Carbon::parse($from_hour)->diffInSeconds(Carbon::parse($to_hour)) / 3600;
        $this->employee_name = 'endDate';
    }

    public function passes($attribute, $value)
    {
        $start = Carbon::parse($this->startDate)->format('Y-m-d');
        $end = Carbon::parse($this->endDate)->format('Y-m-d');
        $employee = Employee::find($value);
        if (!$employee) {
            return false;
        }
        $exists = $employee->vacations()->where(function ($query) use ($start, $end) {
            $query->where(function ($query) use ($start, $end) {
                $query->whereBetween('start_date', [$start, $end]);
            })->orWhere(function ($query) use ($start, $end) {
                $query->whereBetween('end_date', [$start, $end]);
            })->orWhere(function ($query) use ($start, $end) {
                $query->where('start_date', '<=', $start)->where('end_date', '>=', $end);
            });
        })->whereNotIn('status', [
            VacationStatusEnum::CANCELED->value,
            VacationStatusEnum::DECLINED->value
        ])->exists();
        if ($exists) {
            $this->employee_name = $employee->user->name;
            return false;
        }
        return true;
    }

    public function message()
    {
        return $this->employee_name . _t(' already has a vacation for this period of time');
    }
}
