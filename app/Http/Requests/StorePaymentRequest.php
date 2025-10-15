<?php

namespace App\Http\Requests;

use App\Enums\PaymentTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePaymentRequest extends FormRequest
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
            'model_type' => ['required'],
            'model_id' => ['required'],
            'type' => ['required', Rule::in(array_column(PaymentTypeEnum::cases(), 'value'))],
            'amount' => ['required', 'numeric'],
            'file' => ['nullable', 'file', 'max:2048'],
        ];
    }

    public function afterValidation()
    {
        $data = $this->validated();
        return $data;
    }
}
