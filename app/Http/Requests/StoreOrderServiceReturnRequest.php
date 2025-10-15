<?php

namespace App\Http\Requests;

use App\Enums\PaymentTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrderServiceReturnRequest extends FormRequest
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
            'services' => ['required', 'array'],
            'services.*.return' => ['nullable'],
            'services.*.quantity' => ['required', 'numeric', 'min:1'],
            'services.*.payment_type' => ['required', Rule::in(array_column(PaymentTypeEnum::cases(), 'value'))],
        ];
    }

    public function afterValidation()
    {
        $data = $this->validated();
        $data['order_services'] = array();
        foreach ($data['services'] as $index => $service) {
            if (isset($service['return'])) {
                $data['order_services'][$index]['order_service_id'] = $index;
                $data['order_services'][$index]['quantity'] = $service['quantity'];
                $data['order_services'][$index]['payment_type'] = $service['payment_type'];
            }
        }
        return $data;
    }
}
