<?php

namespace App\Http\Requests;

use App\Enums\ConsumptionTypeEnum;
use App\Enums\DepartmentEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SearchStockRequest extends FormRequest
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
        if ($this->q) {
            $this->merge([
                'name' => $this->q
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
            'name' => ['string'],
            'min_quantity' => ['numeric'],
            'excluded_ids' => ['array'],
            'excluded_ids.*' => ['exists:stocks,id'],
            'department' => ['nullable', Rule::in(array_column(DepartmentEnum::cases(), 'value'))],
            'consumption_types' => ['nullable', 'array'],
            'consumption_types.*' => ['required', Rule::in(array_column(ConsumptionTypeEnum::cases(), 'value'))],
            'bill_id' => ['nullable', 'exists:bills,id'],
        ];
    }
}
