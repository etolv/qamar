<?php

namespace App\Http\Requests;

use App\Enums\ConsumptionTypeEnum;
use App\Enums\DepartmentEnum;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class StoreProductRequest extends FormRequest
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
            'sku' => ['required', 'string', 'max:255', 'unique:products,sku,' . $this->id],
            'category_id' => ['required', 'exists:categories,id'],
            'brand_id' => ['nullable', 'exists:brands,id'],
            'min_quantity' => ['required', 'numeric', 'min:0'],
            'department' => ['required', Rule::in(array_column(DepartmentEnum::cases(), 'value'))],
            'refundable' => ['nullable'],
            'consumption_type' => ['required', Rule::in(array_column(ConsumptionTypeEnum::cases(), 'value'))],
            'image' => ['nullable', 'image', 'max:4096', 'mimes:jpeg,png,jpg'],
        ];
    }

    public function afterValidation()
    {
        $data = $this->validated();
        $data['refundable'] = isset($data['refundable']);
        return $data;
    }

    // protected function failedValidation(Validator $validator)
    // {
    //     dd($validator->errors());
    // }
}
