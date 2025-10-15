<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderServiceEmployeeRequest extends FormRequest
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
            'order_service_id' => ['required', 'exists:order_service,id'],
            'employee_id' => ['required', 'exists:employees,id'],
        ];
    }

    public function afterValidation()
    {
        $data = $this->validated();
        return $data;
    }
}
