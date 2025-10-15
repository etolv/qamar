<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class StoreBillReturnRequest extends FormRequest
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
            'bill_id' => ['nullable', 'exists:bills,id'],
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'reason' => ['nullable', 'string', 'max:65000'],
            'stocks' => ['required', 'array'],
            'stocks.*.stock_id' => ['required', 'exists:stocks,id'],
            'stocks.*.return_price' => ['required', 'numeric', 'min:0'],
            'stocks.*.quantity' => ['required', 'numeric', 'min:1'],
        ];
    }

    public function afterValidation()
    {
        $data = $this->validated();
        return $data;
    }

    protected function failedValidation(Validator $validator)
    {
        dd($validator->errors());
    }
}
