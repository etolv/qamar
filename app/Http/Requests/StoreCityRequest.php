<?php

namespace App\Http\Requests;

use App\Models\City;
use Illuminate\Foundation\Http\FormRequest;

class StoreCityRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $locales = config('translation.locales');
        $translatedAttributes = City::getTranslatedFields();
        $rules = array();
        $rules['state_id'] = ['required', 'exists:states,id'];
        foreach ($translatedAttributes as $translatedAttribute) {
            foreach ($locales as $index => $locale) {
                $rules[$index . '.' . $translatedAttribute] = 'required|string';
            }
        }
        $rules['image'] = ['nullable', 'image', 'max:4096', 'mimes:jpeg,png,jpg'];
        return $rules;
    }
}
