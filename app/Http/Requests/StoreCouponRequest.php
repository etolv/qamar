<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class StoreCouponRequest extends FormRequest
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
            'code' => ['required', 'string', 'max:255', 'unique:coupons'],
            'from_date' => ['required', 'date', 'after_or_equal:today'], //. Carbon::now()->parse('Y-m-d')
            'to_date' => ['required', 'date', 'after_or_equal:from_date'],
            'discount' => ['required', 'numeric'],
            'all_services' => ['nullable'],
            'all_products' => ['nullable'],
            'services' => ['array'],
            'services.*' => ['exists:services,id'],
            'products' => ['array'],
            'products.*' => ['exists:products,id'],
        ];
    }

    public function afterValidation()
    {
        $data = $this->validated();
        unset($data['all_services'], $data['all_products']);
        return $data;
    }

    protected function failedValidation(Validator $validator)
    {
        if ($validator->errors()->first()) {
            session()->flash('error', $validator->errors()->first());
        }
    }
}
