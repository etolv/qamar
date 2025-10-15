<?php

namespace App\Rules;

use App\Models\Employee;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class EmployeeLeftVacationRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $employee = Employee::find($value);
        if ($employee->remaining_vacation_days == 0) {
            $fail('Employee does not have enough vacation days');
        }
    }
}
