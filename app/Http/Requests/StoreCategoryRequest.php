<?php

namespace App\Http\Requests;

use App\Models\Category;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
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
        $locales = config('translation.locales');
        $translatedAttributes = Category::getTranslatedFields();
        $rules = array();
        foreach ($translatedAttributes as $translatedAttribute) {
            foreach ($locales as $index => $locale) {
                $rules[$index . '.' . $translatedAttribute] = 'required|string';
            }
        }
        $rules['category_id'] = ['nullable', 'exists:categories,id'];
        $rules['image'] = ['nullable', 'image', 'max:4096', 'mimes:jpeg,png,jpg'];
        return $rules;
    }

    protected function failedValidation(Validator $validator)
    {
        if ($validator->errors()->first()) {
            session()->flash('error', $validator->errors()->first());
        }
    }
}
