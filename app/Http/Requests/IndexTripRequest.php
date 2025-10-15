<?php

namespace App\Http\Requests;

use App\Enums\TripStatusEnum;
use App\Models\Driver;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexTripRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function prepareForValidation()
    {
        if (auth()->user()->type instanceof Driver) {
            $this->merge([
                'driver_id' => auth()->user()->type_id
            ]);
        }
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
            'status' => ['nullable', Rule::enum(TripStatusEnum::class)],
        ];
    }
}
