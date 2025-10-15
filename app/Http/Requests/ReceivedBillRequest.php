<?php

namespace App\Http\Requests;

use App\Models\Bill;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class ReceivedBillRequest extends FormRequest
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
            //
        ];
    }

    public function afterValidation($bill_id)
    {
        $data = $this->validated();
        $bill = Bill::find($bill_id);
        if ($bill->received) {
            session()->flash('error', _t('Bill is already received'));
            throw ValidationException::withMessages(['error' => _t('Bill is already received')]);
        }
        return $data;
    }
}
