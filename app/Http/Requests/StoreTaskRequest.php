<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
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
        $rules = array();
        $rules['title'] = ['required', 'string', 'max:255'];
        $rules['description'] = ['required', 'string', 'max:65000'];
        $rules['employees'] = ['required', 'array'];
        $rules['employees.*'] = ['required', 'numeric', 'exists:employees,id'];
        return $rules;
    }

    public function afterValidation()
    {
        $data = $this->validated();
        $data['user_id'] = auth()->id();
        return $data;
    }
}
