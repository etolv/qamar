<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAddressRequest extends FormRequest
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
        $this->merge([
            'customer_id' => auth()->user()->type_id,
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
            'title' => ['required', 'string', 'max:255'],
            'street' => ['required', 'string', 'max:65000'],
            'address' => ['required', 'string', 'max:65000'],
            'lng' => ['required', 'numeric', 'between:-180,180'],
            'lat' => ['required', 'numeric', 'between:-90,90'],
            'municipal_id' => ['required', 'exists:municipals,id'],
            'image' => ['nullable', 'image', 'max:2048'],
            'customer_id' => ['required', 'exists:customers,id'],
        ];
    }
}
