<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSettingRequest extends FormRequest
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
            'key' => ['required', 'string', 'max:255', 'unique:settings'],
            'value' => ['required', 'string', 'max:65000', 'unique:settings'],
            'appear_app' => ['nullable'],
        ];
    }

    public function afterValidation()
    {
        $data = $this->validated();
        $data['appear_app'] = isset($data['appear_app']);
        return $data;
    }
}
