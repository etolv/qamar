<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class StoreStockRequest extends FormRequest
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
            'product_id' => ['required', 'exists:products,id'],
            // 'branch_id' => ['required', 'exists:branches,id'],
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'purchase_unit_id' => ['required', 'exists:units,id'],
            'retail_unit_id' => ['required', 'exists:units,id'],
            'expiration_date' => ['required', 'date'],
            'barcode' => ['required', 'string', 'max:255', 'unique:stocks,barcode'],
            'quantity' => ['required', 'numeric'],
            'purchase_price' => ['required', 'numeric'],
            'price' => ['required', 'numeric'],
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
