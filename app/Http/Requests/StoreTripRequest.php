<?php

namespace App\Http\Requests;

use App\Rules\UniqueTripableIdRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreTripRequest extends FormRequest
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
            'driver_id' => ['required', 'exists:drivers,id'],
            'type' => ['required', 'in:general,booking'],
            'booking_id' => ['required_if:type,booking', 'exists:bookings,id', new UniqueTripableIdRule($this->type)],
            'description' => ['nullable', 'string', 'max:65000'],
            'from' => ['required', 'string', 'max:255'],
            'to' => ['required', 'string', 'max:255'],
            'from_lng' => ['nullable', 'string', 'max:255'],
            'to_lng' => ['nullable', 'string', 'max:255'],
            'from_lat' => ['nullable', 'string', 'max:255'],
            'to_lat' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function afterValidation()
    {
        $data = $this->validated();
        if ($data['type'] !=  'general') {
            $data['tripable_id'] = $data[$data['type'] . '_id'];
            $data['tripable_type'] = 'App\\Models\\' . ucfirst($data['type']);
            unset($data['type']);
        }
        return $data;
    }
}
