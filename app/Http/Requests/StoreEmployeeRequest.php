<?php

namespace App\Http\Requests;

use App\Enums\WeekDaysEnum;
use App\Models\Job;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class StoreEmployeeRequest extends FormRequest
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
            'country_code' => 'nullable',
            'phone' => 'required|numeric|unique:users,phone',
            'vacation_days' => 'required|numeric|min:0',
            'holiday' => ['required', 'integer', Rule::in(array_column(WeekDaysEnum::cases(), 'value'))],
            'email' => ['nullable', 'email', 'unique:users,email'],
            'password' => 'required|string|min:6',
            'employee_no' => ['nullable', 'string', 'unique:employees,employee_no'],
            'image' => 'nullable|image|max:4096|mimes:jpeg,png,jpg',
            'role_id' => 'required|exists:roles,id',
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
            'employee_infos.*.name' => ['required', 'string', 'max:255'],
            'employee_infos.*.value' => ['required', 'string', 'max:255'],
            'employee_infos.*.file' => ['nullable', 'file', 'max:2048'],
        ];
    }

    public function afterValidation()
    {
        $data = $this->validated();
        // $password = str()->password(10); // TODO generate password
        $data['remaining_vacation_days'] = $data['vacation_days'];
        $data['password'] = Hash::make($data['password']);
        if (!isset($data['job_id'])) {
            $data['job_id'] = Job::where('title', 'موظف')->first()->id;
        }
        return $data;
    }

    protected function failedValidation(Validator $validator)
    {
        if ($validator->errors()->first()) {
            session()->flash('error', $validator->errors()->first());
        }
    }
}
