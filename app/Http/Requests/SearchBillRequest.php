<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchBillRequest extends FormRequest
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
            'q' => ['nullable'],
            'supplier_id' => ['nullable', 'exists:suppliers,id']
        ];
    }

    public function afterValidation()
    {
        $data = $this->validated();
        if (isset($data['q']))
            $data['search'] = $data['q'];
        return $data;
    }
}
