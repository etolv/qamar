<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
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
            'name' => 'nullable|string|min:2|max:255',
            'dial_code' => 'nullable',
            'phone' => 'nullable|numeric|unique:users,phone,' . auth()->id(),
            'email' => 'nullable|email|unique:users,email,' . auth()->id(),
            'password' => 'nullable|string|min:6',
            'image' => 'nullable|image|max:4096|mimes:jpeg,png,jpg',
            'city_id' => 'nullable|exists:cities,id',
            // 'role_id' => 'nullable|exists:roles,id',
        ];
    }

    public function afterValidation()
    {
        $data = $this->validated();
        $data['id'] = auth()->id();
        return $data;
    }
}
