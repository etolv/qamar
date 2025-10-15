<?php

namespace App\Http\Requests;

use App\Enums\PaymentStatusEnum;
use App\Enums\PaymentTypeEnum;
use App\Models\Bill;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class StoreBillPaymentRequest extends FormRequest
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
            'type' => ['required', Rule::in(array_column(PaymentTypeEnum::cases(), 'value'))],
            'amount' => ['required', 'numeric'],
            'file' => ['nullable', 'file', 'max:2048'],
            'card_id' => ['nullable', 'exists:cards,id'],
        ];
    }

    public function afterValidation($id)
    {
        $data = $this->validated();
        $bill = Bill::find($id);
        $payments = $bill->payments()->count('amount');
        $data['status'] = PaymentStatusEnum::PAID->value;
        if ($payments + $data['amount'] > $bill->total) {
            session()->flash('error', _t('Payment is greater than bill amount'));
            throw ValidationException::withMessages(['error' => _t('Payment is greater than bill amount')]);
        }
        return $data;
    }
}
