<?php

namespace App\Http\Requests;

use App\Rules\UniqueValueRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;

class UpdateDriverRequest extends FormRequest
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
            'name' => 'required|string|min:2|max:255',
            'dial_code' => 'nullable',
            'country_code' => 'nullable',
            'phone' => ['required', 'numeric', new UniqueValueRule($this->driver, 'App\Models\Driver')],
            'email' => ['required', 'email', new UniqueValueRule($this->driver, 'App\Models\Driver')],
            'image' => 'nullable|image|max:4096|mimes:jpeg,png,jpg',
            'city_id' => ['required', 'exists:cities,id'],
            'branch_id' => ['required', 'exists:branches,id'],
            'password' => 'nullable|string|min:6',
            'nationality_id' => ['required', 'exists:nationalities,id'],
        ];
    }

    public function afterValidation()
    {
        $data = $this->validated();
        if (isset($data['password']))
            $data['password'] = Hash::make($data['password']);
        return $data;
    }
}
