<?php

namespace App\Http\Requests;

use App\Models\Brand;
use Illuminate\Foundation\Http\FormRequest;

class StoreBrandRequest extends FormRequest
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
        $translatedAttributes = Brand::getTranslatedFields();
        $rules = array();
        foreach ($translatedAttributes as $translatedAttribute) {
            foreach ($locales as $index => $locale) {
                $rules[$index . '.' . $translatedAttribute] = 'required|string';
            }
        }
        $rules['slug'] = ['nullable', 'unique:brands,slug,' . $this->id, 'string', 'max:255'];
        $rules['image'] = ['nullable', 'image', 'max:4096', 'mimes:jpeg,png,jpg'];
        return $rules;
    }

    public function afterValidation()
    {
        $data = $this->validated();
        return $data;
    }
}
