<?php

namespace App\Http\Requests;

use App\Enums\SupplierTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSupplierRequest extends FormRequest
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
            'name' => ['nullable', 'string', 'max:255'],
            'dial_code' => ['nullable'],
            'phone' => ['nullable', 'unique:suppliers,phone'],
            'email' => ['nullable', 'email'],
            'city_id' => ['nullable', 'exists:cities,id'],
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
            'address' => ['nullable', 'string', 'max:65000'],
            'company' => ['nullable', 'string', 'max:255'],
            'bank_number' => ['nullable', 'string', 'max:255'],
            'type' => ['required', Rule::in(array_column(SupplierTypeEnum::cases(), 'value'))],
            'tax_number' => ['nullable', 'string', 'max:255'],
            'link' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'max:4096', 'mimes:jpeg,png,jpg'],
            'cards' => ['nullable', 'array'],
            'cards.*.name' => ['required', 'string', 'max:255'],
            'cards.*.number' => ['required', 'string', 'max:255'],
            'cards.*.iban' => ['required', 'string', 'max:255'],
        ];
    }
}
