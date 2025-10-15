<?php

namespace App\Http\Requests;

use App\Models\Branch;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;

class RegisterRequest extends FormRequest
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
            'dial_code' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:255', 'unique:users,phone'],
            'email' => ['nullable', 'email', 'unique:users,email'],
            'password' => ['nullable', 'string', 'min:6'],
            'notification_token' => ['nullable', 'string', 'max:255'],
            'city_id' => ['required', 'exists:cities,id'],
            'address' => ['nullable', 'string', 'max:65000'],
        ];
    }

    public function afterValidation()
    {
        $data = $this->validated();
        $data['password'] = isset($data['password']) ? Hash::make($data['password']) : Hash::make('password');
        $data['branch_id'] = Branch::first()->id;
        return $data;
    }
}
