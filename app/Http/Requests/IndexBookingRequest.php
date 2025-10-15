<?php

namespace App\Http\Requests;

use App\Enums\StatusEnum;
use App\Models\Customer;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexBookingRequest extends FormRequest
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
        if (auth()->user()->type instanceof Customer) {
            $this->merge([
                'customer_id' => auth()->user()->type_id
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
            'customer_id' => ['nullable', 'exists:customers,id'],
            'status' => ['nullable', Rule::enum(StatusEnum::class)],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date'],
            'statuses' => ['nullable', 'array'],
            'statuses.*' => ['required', Rule::enum(StatusEnum::class)],
        ];
    }
}
