<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAdminRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => 'required|string|min:2|max:255',
            'phone' => 'required|numeric|unique:users,phone',
            'dial_code' => 'nullable',
            'country_code' => 'nullable',
            'password' => 'required|string|min:6',
            'image' => 'nullable|image|max:4096|mimes:jpeg,png,jpg',
            'role_id' => 'required|exists:roles,id',
        ];
    }
}
