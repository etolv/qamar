<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class AfterLoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
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
            'otp' => ['required'],
        ];
    }

    public function afterValidation()
    {
        $data = $this->validated();
        if (!User::where('phone', $data['phone'])->where('code', $data['otp'])->exists()) {
            throw ValidationException::withMessages([
                'otp' => _t('Wrong otp'),
            ]);
        }
        return $data;
    }
}
