<?php

namespace App\Http\Requests;

use App\Enums\WeekDaysEnum;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class StoreDriverRequest extends FormRequest
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
            'email' => ['required', 'email', 'indisposable', 'unique:users,email'],
            'image' => 'nullable|image|max:4096|mimes:jpeg,png,jpg',
            'city_id' => ['required', 'exists:cities,id'],
            'branch_id' => ['required', 'exists:branches,id'],
            'password' => 'required|string|min:6',
            'nationality_id' => ['required', 'exists:nationalities,id'],
            // 'vacation_days' => 'required|numeric|min:0',
            // 'holiday' => ['required', 'integer', Rule::in(array_column(WeekDaysEnum::cases(), 'value'))],
            // 'job_id' => ['required', 'exists:jobs,id'],
            // 'birthday' => ['nullable', 'date_format:Y-m-d'],
            // 'residence_expiration' => ['nullable', 'date_format:Y-m-d'],
            // 'insurance_expiration' => ['nullable', 'date_format:Y-m-d'],
            // 'insurance_card_expiration' => ['nullable', 'date_format:Y-m-d'],
            // 'residence_number' => ['nullable', 'string', 'max:255'],
            // 'insurance_company' => ['nullable', 'string', 'max:255'],
            // 'insurance_number' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function afterValidation()
    {
        $data = $this->validated();
        $data['password'] = Hash::make($data['password']);
        $data['salary'] = 0;
        $data['email_verified_at'] = now();
        return $data;
    }

    protected function failedValidation(Validator $validator)
    {
        session()->flash('error', $validator->errors()->first());
    }
}
