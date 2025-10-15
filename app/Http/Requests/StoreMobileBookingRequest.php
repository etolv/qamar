<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreMobileBookingRequest extends FormRequest
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
        $this->merge([
            'customer_id' => Auth::user()->type_id,
            'is_mobile' => true
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'employee_id' => ['nullable', 'exists:employees,id'],
            'driver_id' => ['nullable', 'exists:employees,id'],
            'customer_id' => ['required', 'exists:customers,id'],
            'is_mobile' => ['required', 'boolean'],
            'coupon_id' => ['nullable', 'exists:coupons,id'],
            'description' => ['nullable', 'string', 'max:65000'],
            'date' => ['required', 'date'],
            'stocks' => ['nullable', 'array'],
            'stocks.*.id' => ['required', 'exists:stocks,id'],
            'stocks.*.quantity' => ['required', 'numeric'],
            'services' => ['required', 'array'],
            'services.*.id' => ['required', 'exists:services,id'],
            'services.*.quantity' => ['required', 'numeric'],
            'services.*.employee_id' => ['nullable', 'exists:employees,id'],
            'address_id' => ['required_without:address', 'exists:addresses,id'],
            'address' => ['required_without:address_id', 'string', 'max:65000'],
            'lng' => ['required_without', 'numeric', 'between:-180,180'],
            'lat' => ['required_without', 'numeric', 'between:-90,90'],
        ];
    }
}
