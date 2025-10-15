<?php

namespace App\Http\Requests;

use App\Enums\CustodyStatusEnum;
use App\Models\Custody;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class WasteCustodyRequest extends FormRequest
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
            'reason' => ['required', 'string', 'max:65000']
        ];
    }

    public function afterValidation($id)
    {
        $data = $this->validated();
        $custody = Custody::find($id);
        if ($custody->status != CustodyStatusEnum::USING) {
            session()->flash('error', _t('Custody Status can not be changed'));
            throw ValidationException::withMessages(['error' => _t('Custody Status can not be changed')]);
        }
        return $data;
    }
}
