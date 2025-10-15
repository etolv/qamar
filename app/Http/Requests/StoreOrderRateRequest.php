<?php

namespace App\Http\Requests;

use App\Enums\RateTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrderRateRequest extends FormRequest
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
            'rates' => ['required', 'array'],
            'rates.*.type' => ['required', Rule::in(array_column(RateTypeEnum::cases(), 'value'))],
            'rates.*.rate' => ['required', 'numeric', 'min:0', 'max:5'],
            'rates.*.rate_reason_id' => ['nullable', 'exists:rate_reasons,id'],
            'rates.*.description' => ['nullable', 'string', 'max:65000'],
        ];
    }

    public function afterValidation($id)
    {
        $data = $this->Validated();
        return $data;
    }
}
