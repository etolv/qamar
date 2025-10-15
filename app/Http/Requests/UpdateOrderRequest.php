<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderRequest extends FormRequest
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
            'customer_id' => ['required', 'exists:customers,id'],
            'employee_id' => ['required', 'exists:employees,id'],
            'branch_id' => ['required', 'exists:branches,id'],
            'description' => ['nullable', 'string', 'max:65000'],
            'stocks' => ['nullable', 'array'],
            'stocks.*' => ['required', 'exists:stocks,id'],
            'services' => ['required', 'array'],
            'services.*' => ['required', 'exists:services,id'],
            'services.*.session_count' => ['nullable', 'numeric', 'min:2'],
            'services.*.due_date' => ['required_with:services.*.session_count', 'date'],
            'services.*.session_price' => ['required_with:services.*.session_count', 'numeric'],
        ];
    }

    public function afterValidation($id)
    {
        $data = $this->validated();
        $data['date'] = now();
        return $data;
    }
}
