<?php

namespace App\Http\Requests;

use App\Enums\MonthsEnum;
use App\Enums\OverTimeStatusEnum;
use App\Models\Attendance;
use App\Models\Employee;
use App\Rules\CompletedOvertime;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class StoreGeneratedSalaryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'all_employees' => ['nullable'],
            'employees' => ['required_without:all_employees', 'array'],
            'employees.*' => ['integer', 'exists:employees,id'],
            'month' => ['required', Rule::in(array_column(MonthsEnum::cases(), 'value'))],
        ];
    }

    public function afterValidation()
    {
        $data = $this->validated();
        if (isset($data['all_employees'])) {
            $data['employees'] = Employee::get()->pluck('id')->toArray();
        }
        if (Employee::whereDoesntHave('activeSalary')->whereIn('id', $data['employees'])->exists()) {
            throw ValidationException::withMessages(['employees' => _t('Please complete salary first for employees')]);
        }
        foreach ($data['employees'] as $employee_id) {
            $data['start'] = Carbon::createFromDate(null, $this->month, 1)->startOfMonth()->format('Y-m-d');
            $data['end'] = Carbon::createFromDate(null, $this->month, 1)->endOfMonth()->format('Y-m-d');
            $not_completed_over_time = Attendance::where('employee_id', $employee_id)
                ->whereBetween('date', [$data['start'], $data['end']])->where('extra_hours', '>', 0)
                ->where('overtime_status', OverTimeStatusEnum::PENDING->value)->exists();
            if ($not_completed_over_time) {
                $employee = Employee::find($employee_id);
                throw ValidationException::withMessages(['employees' => _t('Please complete overtime first for employee ') . $employee->user->name]);
            }
        }
        return $data;
    }
}
