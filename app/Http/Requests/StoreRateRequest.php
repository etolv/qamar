<?php

namespace App\Http\Requests;

use App\Enums\RateTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class StoreRateRequest extends FormRequest
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
            'booking_id' => ['required', 'exists:bookings,id'],
            'rates' => ['required', 'array'],
            'rates.*.type' => ['required', Rule::in(array_column(RateTypeEnum::cases(), 'name'))],
            'rates.*.rate' => ['nullable', 'numeric', 'min:0', 'max:5'],
            'rates.*.description' => ['nullable', 'string', 'max:65000'],
        ];
    }
}
