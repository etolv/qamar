<?php

namespace App\Http\Requests;

use App\Enums\CustodyStatusEnum;
use App\Models\Custody;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ReturnCustodyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    // public function prepareForValidation()
    // {
    //     //
    // }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'custody_id' => ['required', Rule::exists('custodies', 'id')->where('status', CustodyStatusEnum::USING->value)],
            'reason' => ['required', 'string', 'max:65000'],
            'quantity' => ['required', 'integer', 'min:1', function ($attribute, $value, $fail) {
                if (Custody::where('quantity', '>=', $value)->doesntExist()) {
                    $message = _t('Quantity not available');
                    $fail($message);
                }
            }]
        ];
    }
}
