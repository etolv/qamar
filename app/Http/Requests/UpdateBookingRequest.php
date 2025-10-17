<?php

namespace App\Http\Requests;

use App\Enums\PaymentStatusEnum;
use App\Enums\StatusEnum;
use App\Models\Booking;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UpdateBookingRequest extends FormRequest
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
            'customer_id' => ['nullable', 'exists:customers,id'],
            'employee_id' => ['nullable', 'exists:employees,id'],
            'driver_id' => ['nullable', 'exists:employees,id'],
            'description' => ['nullable', 'string', 'max:65000'],
            'date' => ['nullable', 'date'],
            'stocks' => ['nullable', 'array'],
            'stocks.*' => ['nullable', 'exists:stocks,id'],
            'services' => ['nullable', 'array'],
            'services.*' => ['nullable', 'exists:services,id'],
            'address' => ['nullable', 'string', 'max:65000'],
            'lng' => ['nullable', 'numeric', 'between:-180,180'],
            'lat' => ['nullable', 'numeric', 'between:-90,90'],
            'status' => ['nullable', Rule::enum(StatusEnum::class)],
            'coupon_id' => ['nullable', 'exists:coupons,id'],

        ];
    }
    public function afterValidation($id)
    {
        $data = $this->validated();
        $booking = Booking::find($id);
        $data['coupon_id'] = $this->input('coupon_id');

      if (in_array($data['status'], [StatusEnum::CONFIRMED->value, StatusEnum::STARTED->value, StatusEnum::COMPLETED->value]) && $booking->payment_status != PaymentStatusEnum::PAID) {
            session()->flash('error', _t('Booking status can not be updated until payment is done'));
            throw ValidationException::withMessages(['error' => _t('Booking status can not be updated until payment is done')]);
        } else if (in_array($data['status'], [StatusEnum::CANCELED->value, StatusEnum::REJECTED->value])) {
            session()->flash('error', _t('Booking status can not be updated since it is already canceled'));
            throw ValidationException::withMessages(['error' => _t('Booking status can not be updated since it is already canceled')]);
        }
        return $data;
    }
}
