<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLoyaltyRequest extends FormRequest
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
            'cash' => ['required', 'integer', 'min:0'],
            'points' => ['required', 'integer', 'min:0'],
            'return' => ['required', 'integer', 'min:0'],
        ];
    }

    public function afterValidation()
    {
        $data = $this->validated();
        $data['cash_to_points'] = $data['points'] / $data['cash']; // each 1 cash will give X point
        $data['points_to_cash'] = $data['return'] / $data['points']; // each 1 point will give X cash
        unset($data['cash']);
        unset($data['points']);
        unset($data['return']);
        return $data;
    }
}
