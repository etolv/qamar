<?php

namespace App\Http\Requests;

use App\Models\Municipal;
use Illuminate\Foundation\Http\FormRequest;

class StoreMunicipalRequest extends FormRequest
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
        $translatedAttributes = Municipal::getTranslatedFields();
        $rules = array();
        $rules['delivery_fee'] = ['required', 'numeric', 'min:0'];
        $rules['city_id'] = ['required', 'exists:cities,id'];
        foreach ($translatedAttributes as $translatedAttribute) {
            foreach ($locales as $index => $locale) {
                $rules[$index . '.' . $translatedAttribute] = 'required|string';
            }
        }
        $rules['image'] = ['nullable', 'image', 'max:4096', 'mimes:jpeg,png,jpg'];
        return $rules;
    }
}
