<?php

namespace App\Http\Requests;

use App\Models\Admin;
use App\Models\User;
use App\Rules\ValidateAdminLogin;
use Illuminate\Foundation\Http\FormRequest;

class AdminLoginRequest extends FormRequest
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
            'phone' => ['required', 'exists:users,phone', new ValidateAdminLogin],
            'password' => ['required', 'string'],
            'remember' => ['nullable'],
        ];
    }

    public function afterValidation()
    {
        $data = $this->validated();
        $user = User::where('phone', $data['phone'])->first();
        // $data['is_admin'] = $user->account instanceof Admin;
        $data['remember'] = isset($data['remember']);
        return $data;
    }
}
