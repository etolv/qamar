<?php

namespace App\Http\Requests;

use App\Enums\DepartmentEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SearchProductReqeust extends FormRequest
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
            'q' => ['string'],
            'min_quantity' => ['numeric'],
            'excluded_ids' => ['array'],
            'excluded_ids.*' => ['exists:stocks,id'],
            'department' => ['nullable', Rule::in(array_column(DepartmentEnum::cases(), 'value'))],
        ];
    }
}
