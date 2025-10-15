<?php

namespace App\Http\Requests;

use App\Models\Driver;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FunctionalityNotAvailable;

class UpdateDriverLocationRequest extends FormRequest
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
            'driver_id' => ['nullable', 'exists:drivers,id'],
            'lat' => ['nullable', 'numeric', 'between:-90,90'],
            'lng' => ['nullable', 'numeric', 'between:-180,180'],
        ];
    }

    public function afterValidation()
    {
        $data = $this->validated();
        $user = Auth::user();
        if ($user->account instanceof Driver) {
            $data['driver_id'] = $user->type_id;
        }
        return $data;
    }
}
