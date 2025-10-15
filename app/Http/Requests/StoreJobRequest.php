<?php

namespace App\Http\Requests;

use App\Enums\SectionEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreJobRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255'],
            'section' => ['required', Rule::in(array_column(SectionEnum::cases(), 'value'))],
            'description' => ['nullable', 'string', 'max:65000'],
        ];
    }
}
