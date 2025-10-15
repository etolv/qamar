<?php

namespace App\Http\Requests;

use App\Enums\DepartmentEnum;
use App\Enums\OrderableTypeEnum;
use App\Enums\PaymentStatusEnum;
use App\Enums\PaymentTypeEnum;
use App\Enums\StatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCafeteriaOrderRequest extends FormRequest
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
            'type' => ['required', Rule::in(array_column(OrderableTypeEnum::cases(), 'value'))],
            'customer_id' => ['required_if:orderable_type,2', 'exists:customers,id'],
            'employee_id' => ['required_if:orderable_type,3', 'exists:employees,id'],
            'branch_id' => ['required', 'exists:branches,id'],
            // 'coupon_id' => ['nullable', 'exists:coupons,id'],
            'description' => ['nullable', 'string', 'max:65000'],
            'stocks' => ['nullable', 'array'],
            'stocks.*.id' => ['required', 'exists:stocks,id'],
            'stocks.*.quantity' => ['required', 'numeric'],
            'services' => ['nullable', 'array'],
            'services.*.id' => ['required', 'exists:services,id'],
            'services.*.quantity' => ['required', 'numeric'],
            'payments' => ['required', 'array', 'min:1'],
            'payments.*.amount' => ['required', 'numeric', 'min:0'],
            'payments.*.type' => ['required', Rule::in(array_column(PaymentTypeEnum::cases(), 'value'))],
            'payments.*.paid' => ['nullable'],
            'tax_included' => ['nullable'],
            'left' => ['nullable', 'min:0'],
        ];
    }

    public function afterValidation()
    {
        $data = $this->validated();
        foreach ($data['payments'] as &$payment) {
            $payment['status'] = isset($payment['paid']) ? PaymentStatusEnum::PAID->value : PaymentStatusEnum::PENDING->value;
            unset($payment['paid']);
        }
        $data['payment_status'] = PaymentStatusEnum::PAID->value;
        $data['status'] = StatusEnum::COMPLETED->value;
        $data['tax_included'] = isset($data['tax_included']);
        if (isset($data['left']) && $data['left'] < 0) {
            $data['left'] *= -1;
        }
        return $data;
    }
}
