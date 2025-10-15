<?php

namespace App\Rules;

use App\Enums\VacationTypeEnum;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;

class SufficientVacationDays implements Rule
{
    protected $startDate;
    protected $endDate;
    protected $vacationType;
    protected $hours;
    protected $left;
    protected $employee_name;

    /**
     * Create a new rule instance.
     *
     * @param int $employeeId
     * @param string $startDate
     * @param string $endDate
     * @param int $vacationType
     */
    public function __construct($startDate, $endDate, $vacationType, $from_hour, $to_hour)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        if (!$endDate) {
            $this->endDate = $startDate;
        }
        $this->vacationType = $vacationType;
        $this->employee_name = '';
        $this->hours = Carbon::parse($from_hour)->diffInSeconds(Carbon::parse($to_hour)) / 3600;
        $this->left = 0;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // Calculate the number of vacation days requested
        $start = Carbon::parse($this->startDate);
        $end = Carbon::parse($this->endDate);
        $daysRequested = $end->diffInDays($start) + 1;
        if ($this->hours) {
            $daysRequested = $this->hours / 8;
        }

        // Fetch the employee and check vacation days
        $employee = Employee::find($value);

        if (!$employee) {
            return false; // Employee not found
        }

        // If it's an annual vacation, check if the employee has enough remaining days
        if ($this->vacationType == VacationTypeEnum::ANNUAL->value && $employee->remaining_vacation_days < $daysRequested) {
            $this->left = $employee->remaining_vacation_days;
            $this->employee_name = $employee->user->name;
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->employee_name . _t(' does not have enough vacation days for annual vacation, left vacations: ' . $this->left);
    }
}
