<?php

namespace App\Http\Requests;

use App\Enums\DepartmentEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;

class StoreServiceRequest extends FormRequest
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
            'sku' => ['nullable', 'string', 'max:255', 'unique:services,sku,' . $this->id],
            'description' => ['nullable', 'string', 'max:65000'],
            'category_id' => ['required', 'exists:categories,id'],
            'sub_categories' => ['nullable', 'array'],
            'sub_categories.*' => ['required', 'exists:categories,id'],
            'price' => ['required', 'numeric', 'min:0'],
            'image' => ['nullable', 'image', 'max:4096', 'mimes:jpeg,png,jpg'],
            'has_terms' => ['nullable'],
            'products' => ['nullable', 'array'],
            'products.*.id' => ['required', 'exists:products,id'],
            'products.*.required' => ['nullable'],
            'terms' => ['nullable', 'file', 'max:4096'],
            'department' => ['required', Rule::in(array_column(DepartmentEnum::cases(), 'value'))],
        ];
    }

    public function afterValidation()
    {
        $data = $this->validated();
        $data['has_terms'] = isset($data['has_terms']);
        return $data;
    }

    protected function failedValidation(Validator $validator)
    {
        dd($validator->errors());
    }
}
