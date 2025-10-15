<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class StorePackageRequest extends FormRequest
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
            'image' => ['nullable', 'image', 'max:4096'],
            'name' => ['required', 'string', 'max:255'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
            'description' => ['nullable', 'string', 'max:65000'],
            'stocks' => ['required_without:services', 'array'],
            'stocks.*.id' => ['nullable', 'exists:stocks,id'],
            'stocks.*.quantity' => ['required', 'numeric'],
            'stocks.*.price' => ['required', 'numeric'],
            'services' => ['required_without:stocks', 'array'],
            'services.*.id' => ['required', 'exists:services,id'],
            'services.*.quantity' => ['required', 'numeric'],
            'services.*.price' => ['required', 'numeric'],
        ];
    }

    public function afterValidation()
    {
        $data = $this->validated();
        return $data;
    }

    // protected function failedValidation(Validator $validator)
    // {
    //     if ($validator->errors()->first()) {
    //         session()->flash('error', $validator->errors()->first());
    //         return redirect()->back()->withInput();
    //     }
    // }
}
