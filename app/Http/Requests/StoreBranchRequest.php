<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBranchRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'city_id' => ['required', 'exists:cities,id'],
            'is_physical' => ['nullable'],
            'address' => ['required', 'string', 'max:65000'],
            'street' => ['nullable', 'string', 'max:65000'],
            'building' => ['nullable', 'string', 'max:65000'],
        ];
    }

    public function afterValidation()
    {
        $data = $this->validated();
        $data['is_physical'] = isset($data['is_physical']);
        return $data;
    }
}
