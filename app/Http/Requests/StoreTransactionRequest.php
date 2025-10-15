<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
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
            'from_account_id' => ['required', 'exists:accounts,id'],
            'to_account_id' => ['required', 'exists:accounts,id'],
            'amount' => ['required', 'min:0'],
            'description' => ['required', 'string', 'max:1000']
        ];
    }

    public function afterValidation()
    {
        $data = $this->validated();
        return $data;
    }

    protected function failedValidation(Validator $validator)
    {
        if ($validator->errors()->first()) {
            session()->flash('error', $validator->errors()->first());
        }
    }
}
