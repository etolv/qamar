<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAddressRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function prepareForValidation(): void
    {
        $this->merge([
            'customer_id' => auth()->user()->type_id,
            'address_id' => $this->address,
            'address' => $this->address_name
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['nullable', 'string', 'max:255'],
            'street' => ['nullable', 'string', 'max:65000'],
            'address' => ['nullable', 'string', 'max:65000'],
            'lng' => ['nullable', 'numeric', 'between:-180,180'],
            'lat' => ['nullable', 'numeric', 'between:-90,90'],
            'municipal_id' => ['nullable', 'exists:municipals,id'],
            'image' => ['nullable', 'image', 'max:2048'],
            'address_id' => ['required', 'exists:addresses,id'],
            'customer_id' => [
                'required',
                'exists:customers,id',
                Rule::exists('addresses', 'customer_id')->where('id', $this->address_id),
            ],

        ];
    }
}
