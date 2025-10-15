<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeSettingRequest extends FormRequest
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
            'employee_minimum_profit' => ['required', 'numeric', 'min:0'],
            'employee_profit_percentage' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function afterValidation()
    {
        $data = $this->validated();
        return $data;
    }
}
