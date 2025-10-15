<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class FilterCustomerRequest extends FormRequest
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
            'visit_range' => ['nullable', 'string'],
            'last_visit' => ['nullable'],
            'visit_count' => ['nullable', 'min:0']
        ];
    }

    public function afterValidation(): array
    {
        $data = $this->validated();
        if (isset($data['date']) && str_contains($data['date'], ' to ')) {
            [$data['start'], $data['end']] = explode(' to ', $data['date']);
        }
        if (isset($data['visit_range']) && str_contains($data['visit_range'], ' to ')) {
            [$data['start_visit'], $data['end_visit']] = explode(' to ', $data['visit_range']);
        }
        return $data;
    }
}
