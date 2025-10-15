<?php

namespace App\Http\Requests;

use App\Rules\UniqueValueRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class UpdateCustomerRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'dial_code' => ['nullable'],
            'phone' => ['required', 'unique:users,phone,' . $this->customer],
            'city_id' => ['required', 'exists:cities,id'],
            'branch_id' => ['required', 'exists:branches,id'],
            'address' => ['nullable', 'string', 'max:65000'],
            'password' => ['nullable', 'string', 'min:6'],
            'image' => ['nullable', 'image', 'max:4096', 'mimes:jpeg,png,jpg'],
        ];
    }

    // protected function failedValidation(Validator $validator)
    // {
    //     dd($validator->errors());
    // }

    public function afterValidation()
    {
        $data = $this->validated();
        return $data;
    }
}
