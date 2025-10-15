<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
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
            'customer_id' => ['required', 'exists:customers,id'],
            'employee_id' => ['required', 'exists:employees,id'],
            'driver_id' => ['nullable', 'exists:employees,id'],
            'coupon_id' => ['nullable', 'exists:coupons,id'],
            'description' => ['nullable', 'string', 'max:65000'],
            'date' => ['required', 'date'],
            // 'stocks' => ['nullable', 'array'],
            // 'stocks.*' => ['required', 'exists:stocks,id'],
            // 'services' => ['required', 'array'],
            // 'services.*' => ['required', 'exists:services,id'],
            'stocks' => ['nullable', 'array'],
            'stocks.*.id' => ['required', 'exists:stocks,id'],
            'stocks.*.quantity' => ['required', 'numeric'],
            'services' => ['required', 'array'],
            'services.*.id' => ['required', 'exists:services,id'],
            'services.*.quantity' => ['required', 'numeric'],
            'services.*.employee' => ['nullable', 'exists:employees,id'],
            'address_id' => ['required_without:address', 'exists:addresses,id'],
            'address' => ['required_without:address_id', 'string', 'max:65000'],
            'lng' => ['required_without', 'numeric', 'between:-180,180'],
            'lat' => ['required_without', 'numeric', 'between:-90,90'],
        ];
    }

    public function afterValidation()
    {
        $data = $this->validated();
        return $data;
    }
}
