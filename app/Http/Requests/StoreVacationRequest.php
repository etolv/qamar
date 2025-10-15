<?php

namespace App\Http\Requests;

use App\Enums\VacationStatusEnum;
use App\Enums\VacationTypeEnum;
use App\Models\Employee;
use App\Rules\SufficientVacationDays;
use App\Rules\UniqueEmployeeVacation;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class StoreVacationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create_vacation') ||  (count($this->employees) == 1 && $this->employees[0] == $this->user()->type_id);
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
            'employees.*' => [
                'required',
                'exists:employees,id',
                new SufficientVacationDays($this->start_date, $this->end_date, $this->type, $this->from_hour, $this->to_hour),
                new UniqueEmployeeVacation($this->start_date, $this->end_date, $this->from_hour, $this->to_hour)
            ],
            'is_hourly' => ['nullable'],
            'start_date' => ['required', 'date_format:Y-m-d'],
            'end_date' => ['required_without:is_hourly', 'nullable', 'date_format:Y-m-d', 'after_or_equal:start_date'],
            // 'hours' => ['required_with:is_hourly', 'nullable', 'numeric', 'min:1', 'max:8'],
            'from_hour' => ['required_with:is_hourly', 'nullable', 'date_format:H:i'],
            'to_hour' => ['required_with:is_hourly', 'nullable', 'date_format:H:i', 'after:from_hour'],
            'reason' => ['nullable', 'string', 'max:65000'],
            'type' => ['required', 'integer', Rule::in(array_column(VacationTypeEnum::cases(), 'value'))],
            'status' => ['required', 'integer', Rule::in(array_column(VacationStatusEnum::cases(), 'value'))],
            'file' => ['nullable', 'file', 'max:2048'],
        ];
    }

    public function afterValidation()
    {
        $data = $this->validated();
        $data['is_hourly'] = isset($data['is_hourly']);
        if ($data['is_hourly']) {
            $data['hours'] = Carbon::parse($data['to_hour'])->diffInSeconds(Carbon::parse($data['from_hour'])) / 3600;
            $data['end_date'] = $data['start_date'];
            $data['days'] = $data['hours'] / 8;
        } else {
            $data['days'] = Carbon::parse($data['end_date'])->diffInDays(Carbon::parse($data['start_date'])) + 1;
        }
        // $employee = Employee::find($data['employee_id']);
        // if ($employee->remaining_vacation_days < $data['days'] && $data['type'] == VacationTypeEnum::ANNUAL->value) {
        //     throw ValidationException::withMessages(['employee_id' => _t('Employee does not have enough vacation days for annual vacation')]);
        // }
        return $data;
    }

    // protected function failedValidation(Validator $validator)
    // {
    //     dd($validator->errors());
    // }
}
