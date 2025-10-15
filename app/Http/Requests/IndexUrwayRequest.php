<?php

namespace App\Http\Requests;

use App\Enums\PaymentStatusEnum;
use App\Helpers\Helpers;
use Illuminate\Foundation\Http\FormRequest;

class IndexUrwayRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Helpers::validateUrwayHashedResponse($this);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "PaymentId" => ['nullable'],
            "TranId" => ['nullable'],
            "ECI" => ['nullable'],
            "Result" => ['nullable'],
            "TrackId" => ['nullable'],
            "AuthCode" => ['nullable'],
            "ResponseCode" => ['nullable'],
            "responseHash" => ['nullable'],
            "amount" => ['nullable'],
            "cardBrand" => ['nullable'],
            "RRN" => ['nullable'],
            "UserField1" => ['nullable'],
            "UserField3" => ['nullable'],
            "UserField4" => ['nullable'],
            "UserField5" => ['nullable'],
            "maskedPAN" => ['nullable'],
            "cardToken" => ['nullable'],
            "SubscriptionId" => ['nullable'],
            "email" => ['nullable'],
            "payFor" => ['nullable'],
            "PaymentType" => ['nullable'],
            "metaData" => ['nullable'],
        ];
    }

    public function afterValidation()
    {
        $data = $this->validated();
        $payment_data = array();
        $payment_data['data'] = json_encode($data);
        $payment_data['payment_id'] = $data['TrackId'];
        $payment_data['status'] = $data['Result'] == 'Successful' ? PaymentStatusEnum::PAID->value : PaymentStatusEnum::FAILED->value;
        return $payment_data;
    }
}
