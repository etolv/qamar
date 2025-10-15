<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FilterBillRequest extends FormRequest
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
            'date' => ['nullable', 'string'],
        ];
    }

    public function afterValidation(): array
    {
        $data = $this->validated();
        if (isset($data['date']) && str_contains($data['date'], ' to ')) {
            [$data['start'], $data['end']] = explode(' to ', $data['date']);
        }
        return $data;
    }
}
