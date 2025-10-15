<?php

namespace App\Http\Requests;

use App\Models\Transaction;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class DestroyTransactionRequest extends FormRequest
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

    public function afterValidation($id)
    {
        $data = $this->validated();
        if (!Transaction::whereId($id)->where('is_automatic', false)->exists()) {
            session()->flash('error', _t("Auto generated transactions are not deletable"));
            throw ValidationException::withMessages(['error' => _t("Auto generated transactions are not deletable")]);
        }
        return $data;
    }
}
