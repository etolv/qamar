<?php

namespace App\Http\Requests;

use App\Enums\WeekDaysEnum;
use App\Rules\UniqueValueRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UpdateEmployeeRequest extends FormRequest
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
            'name' => 'required|string|min:2|max:255',
            'dial_code' => 'nullable',
            'phone' => ['required', 'numeric', new UniqueValueRule($this->employee, 'App\Models\Employee')],
            'email' => ['nullable', 'email', new UniqueValueRule($this->employee, 'App\Models\Employee')],
            'password' => 'nullable|string|min:6',
            'employee_no' => ['nullable', 'string', 'unique:employees,employee_no,' . $this->employee],
            'image' => 'nullable|image|max:4096|mimes:jpeg,png,jpg',
            'role_id' => 'required|exists:roles,id',
            'vacation_days' => 'required|numeric|min:0',
            'holiday' => ['required', 'integer', Rule::in(array_column(WeekDaysEnum::cases(), 'value'))],
            'city_id' => ['required', 'exists:cities,id'],
            'branch_id' => ['required', 'exists:branches,id'],
            'job_id' => ['nullable', 'exists:jobs,id'],
            'nationality_id' => ['required', 'exists:nationalities,id'],
            'birthday' => ['nullable', 'date_format:Y-m-d'],
            'start_work' => ['nullable', 'date_format:Y-m-d'],
            'residence_expiration' => ['nullable', 'date_format:Y-m-d'],
            'insurance_expiration' => ['nullable', 'date_format:Y-m-d'],
            'insurance_card_expiration' => ['nullable', 'date_format:Y-m-d'],
            'residence_number' => ['nullable', 'string', 'max:255'],
            'insurance_company' => ['nullable', 'string', 'max:255'],
            'insurance_number' => ['nullable', 'string', 'max:255'],
            'health_number' => ['nullable', 'string', 'max:255'],
            'passport_number' => ['nullable', 'string', 'max:255'],
            'passport_expiration' => ['nullable', 'date_format:Y-m-d'],
            'employee_infos' => ['nullable', 'array'],
            'employee_infos.*.id' => ['nullable', 'exists:employee_infos,id'],
            'employee_infos.*.name' => ['required', 'string', 'max:255'],
            'employee_infos.*.value' => ['required', 'string', 'max:255'],
            'employee_infos.*.file' => ['nullable', 'file', 'max:2048'],
        ];
    }

    public function afterValidation()
    {
        $data = $this->validated();
        // if (!isset($data['job_id'])) {
        //     $data['job_id'] = Job::where('title', 'موظف')->first()->id;
        // }
        // if (isset($data['password']))
        //     $data['password'] = Hash::make($data['password']);
        return $data;
    }

    // protected function failedValidation(Validator $validator)
    // {
    //     dd($validator->errors());
    // }
}
