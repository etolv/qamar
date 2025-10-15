<?php

namespace App\Http\Requests;

use App\Models\State;
use Illuminate\Foundation\Http\FormRequest;

class StoreStateRequest extends FormRequest
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
        $translatedAttributes = State::getTranslatedFields();
        $rules = array();
        foreach ($translatedAttributes as $translatedAttribute) {
            foreach ($locales as $index => $locale) {
                $rules[$index . '.' . $translatedAttribute] = 'required|string';
            }
        }
        $rules['image'] = ['nullable', 'image', 'max:4096', 'mimes:jpeg,png,jpg'];
        return $rules;
    }
}
