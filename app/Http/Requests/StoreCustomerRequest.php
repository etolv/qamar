<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'dial_code' => ['nullable'],
            'phone' => ['required', 'numeric', 'unique:users'],
            'city_id' => ['required', 'exists:cities,id'],
            'branch_id' => ['required', 'exists:branches,id'],
            'address' => ['nullable', 'string', 'max:65000'],
            'password' => ['required', 'string', 'min:6'],
            'image' => ['nullable', 'image', 'max:4096', 'mimes:jpeg,png,jpg'],
        ];
    }

    public function afterValidation()
    {
        $data = $this->validated();
        $data['verified'] = true;
        return $data;
    }
}
