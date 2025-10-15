<?php

namespace App\Http\Requests;

use App\Enums\DepartmentEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SearchServiceRequest extends FormRequest
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
            'excluded_ids' => ['array'],
            'excluded_ids.*' => ['exists:services,id'],
            'department' => ['nullable', Rule::in(array_column(DepartmentEnum::cases(), 'value'))],
        ];
    }
}
