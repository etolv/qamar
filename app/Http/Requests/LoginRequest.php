<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Rules\ValidatePasswordRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
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
            'phone' => ['required', 'exists:users,phone'],
            "password" => ['required'],
            'notification_token' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function afterValidation()
    {
        $data = $this->validated();
        if (!Hash::check($data['password'], (User::where('phone', $data['phone']))->first()->password)) {
            throw ValidationException::withMessages(['password' => _t("Wrong password")]);
        }
        return $data;
    }
}
