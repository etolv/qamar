<?php

namespace App\Http\Requests;

use App\Enums\TripStatusEnum;
use App\Models\Admin;
use App\Models\Trip;
use App\Rules\UniqueTripableIdRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTripRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->type instanceof Admin ? true : Trip::where('driver_id', auth()->user()->type_id)->where('id', $this->trip)->exists();;
    }

    public function prepareForValidation()
    {
        if ($this->type == 'general') {
            $this->merge([
                'tripable_id' => null,
                'tripable_type' => null,
            ]);
        } else {
            $this->merge([
                'tripable_id' => $this->{$this->type . '_id'},
                'tripable_type' => 'App\\Models\\' . ucfirst($this->type),
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
            'type' => ['nullable', 'in:general,booking'],
            'booking_id' => ['nullable', 'exists:bookings,id', new UniqueTripableIdRule($this->type, $this->trip)],
            'status' => ['nullable', Rule::in(array_column(TripStatusEnum::cases(), 'value'))],
            'description' => ['nullable', 'string', 'max:65000'],
            'from' => ['nullable', 'string', 'max:255'],
            'to' => ['nullable', 'string', 'max:255'],
            'from_lng' => ['nullable', 'string', 'max:255'],
            'to_lng' => ['nullable', 'string', 'max:255'],
            'from_lat' => ['nullable', 'string', 'max:255'],
            'to_lat' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function afterValidation()
    {
        $data = $this->validated();
        unset($data['type']);
        return $data;
    }
}
