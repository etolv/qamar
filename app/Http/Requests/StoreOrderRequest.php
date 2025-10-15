<?php

namespace App\Http\Requests;

use App\Enums\DepartmentEnum;
use App\Enums\PaymentStatusEnum;
use App\Enums\PaymentTypeEnum;
use App\Models\Coupon;
use App\Rules\ValidateOrderServiceProducts;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrderRequest extends FormRequest
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
            'employee_id' => ['nullable', 'exists:employees,id'],
            'branch_id' => ['required', 'exists:branches,id'],
            'coupon_id' => ['nullable', 'exists:coupons,id'],
            'description' => ['nullable', 'string', 'max:65000'],
            'type' => ['required', 'string', 'in:normal,gift'],
            'gifter_id' => ['required_if:type,gift', 'nullable'],
            'gift_end_date' => ['required_if:type,gift', 'date', 'nullable'],
            'department' => ['nullable', Rule::in(array_column(DepartmentEnum::cases(), 'value'))],
            'points' => ['nullable'],
            'tax_included' => ['nullable'],
            'stocks' => ['nullable', 'array'],
            'stocks.*.id' => ['required', 'exists:stocks,id'],
            'stocks.*.quantity' => ['required', 'numeric'],
            'services' => ['nullable', 'array'],
            'services.*.id' => ['required', 'exists:services,id', new ValidateOrderServiceProducts],
            'services.*.quantity' => ['required', 'numeric'],
            'services.*.employee' => ['nullable', 'exists:employees,id'],
            'services.*.session_count' => ['nullable', 'numeric', 'min:2'],
            'services.*.due_date' => ['required_with:services.*.session_count', 'date'],
            'services.*.session_price' => ['nullable', 'numeric'],
            'payments' => ['required', 'array', 'min:1'],
            'payments.*.amount' => ['required', 'numeric', 'min:0'],
            'payments.*.type' => ['required', Rule::in(array_column(PaymentTypeEnum::cases(), 'value'))],
            'payments.*.card_id' => ['nullable', 'exists:cards,id'],
            'payments.*.paid' => ['nullable'],
            'packages' => ['nullable', 'array'],
            'packages.*.id' => ['required', 'exists:packages,id'],
            'packages.*.stocks' => ['nullable', 'array'],
            'packages.*.stocks.*.id' => ['required', 'exists:package_items,id'],
            'packages.*.services' => ['nullable', 'array'],
            'packages.*.services.*.id' => ['required', 'exists:package_items,id', new ValidateOrderServiceProducts],
            'packages.*.services.*.employee' => ['nullable', 'exists:employees,id'],
            'left' => ['nullable'],
        ];
    }

    public function afterValidation()
    {
        $data = $this->validated();
        $data['payment_status'] = PaymentStatusEnum::PAID->value;
        foreach ($data['payments'] as &$payment) {
            $payment['status'] = isset($payment['paid']) ? PaymentStatusEnum::PAID->value : PaymentStatusEnum::PENDING->value;
            unset($payment['paid']);
            if ($payment['status'] == PaymentStatusEnum::PENDING->value) {
                $data['payment_status'] = PaymentStatusEnum::PENDING->value;
            }
        }
        $data['is_gift'] = $data['type'] == 'gift';
        unset($data['type']);
        if ($data['gifter_id'] == 'admin') {
            unset($data['gifter_id']);
        }
        $data['date'] = now();
        $data['tax_included'] = isset($data['tax_included']);
        if (isset($data['left']) && $data['left'] < 0) {
            $data['left'] *= -1;
        }
        return $data;
    }

    protected function failedValidation(Validator $validator)
    {
        dd($validator->errors());
    }
}
