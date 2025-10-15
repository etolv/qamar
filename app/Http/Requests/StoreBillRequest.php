<?php

namespace App\Http\Requests;

use App\Enums\BillTypeEnum;
use App\Enums\DepartmentEnum;
use App\Enums\PaymentTypeEnum;
use App\Enums\TaxTypeEnum;
use App\Models\Bill;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBillRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function prepareForValidation()
    {
        if (!$this->supplier_id) {
            $this->merge([
                'supplier_id' => $this->main_supplier_id
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'tax_type' => ['nullable', Rule::in(array_column(TaxTypeEnum::cases(), 'value'))],
            'bill_type_id' => ['nullable', 'exists:bill_types,id'],
            'term' => ['nullable', 'string', 'max:255'],
            'department' => ['required', Rule::in(array_column(DepartmentEnum::cases(), 'value'))],
            'payment_type' => ['required', Rule::in(array_column(PaymentTypeEnum::cases(), 'value'))],
            'card_id' => ['nullable', 'exists:cards,id'],
            'type' => ['required'],
            'received' => ['nullable'],
            'paid' => ['required', 'numeric', 'min:0'],
            'total' => ['nullable', 'numeric'],
            'products' => ['nullable', 'array'],
            'products.*.product_id' => ['required', 'exists:products,id'],
            'products.*.quantity' => ['required', 'numeric'],
            'products.*.purchase_price' => ['required', 'numeric'],
            'products.*.sell_price' => ['required', 'numeric'],
            // 'products.*.min_price' => ['required', 'numeric'],
            'products.*.exchange_price' => ['required', 'numeric'],
            'products.*.convert' => ['required', 'numeric'],
            'products.*.expiration_date' => ['nullable', 'date'],
            'products.*.retail_unit_id' => ['required', 'exists:units,id'],
            'products.*.purchase_unit_id' => ['required', 'exists:units,id'],
            'products.*.tax_type' => ['required', Rule::in(array_column(TaxTypeEnum::cases(), 'value'))],
            'file' => ['nullable', 'file', 'max:4096'],
        ];
    }

    public function afterValidation()
    {
        $data = $this->validated();
        $data['received'] = isset($data['received']);
        $data['receiving_date'] = $data['received'] ? Carbon::now() : null;
        $data['type'] = BillTypeEnum::fromName(strtoupper($data['type']))->value;
        if (!isset($data['identifier'])) {
            $bill_id = (Bill::latest()->first()?->id ?? 0) + 1;
            $data['identifier'] = 'B' . $bill_id  . $this->supplier_id . Carbon::now()->format('ymd');
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
