<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBillTypeRequest extends FormRequest
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
            'description' => ['nullable', 'string', 'max:255'],
            'static' => ['nullable'],
            'price' => ['nullable', 'numeric', 'required_if:static,on'],
        ];
    }

    public function afterValidation()
    {
        $data = $this->validated();
        $data['static'] = isset($data['static']);
        if (!$data['static'])
            $data['price'] = 0;
        return $data;
    }
}
