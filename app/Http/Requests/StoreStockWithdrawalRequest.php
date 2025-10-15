<?php

namespace App\Http\Requests;

use App\Enums\DepartmentEnum;
use App\Enums\StockWithdrawalTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;

class StoreStockWithdrawalRequest extends FormRequest
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
            'type' => ['required', Rule::in(array_column(StockWithdrawalTypeEnum::cases(), 'value'))],
            'employee_id' => ['required_if:type,3', 'exists:employees,id'],
            'reason' => ['nullable', 'string', 'max:65000'],
            'department' => ['nullable', Rule::in(array_column(DepartmentEnum::cases(), 'value'))],
            'stocks' => ['required', 'array'],
            'stocks.*.stock_id' => ['required', 'exists:stocks,id'],
            'stocks.*.price' => ['required', 'numeric', 'min:0'],
            'stocks.*.quantity' => ['required', 'numeric', 'min:1'],
        ];
    }

    public function afterValidation()
    {
        $data = $this->validated();
        return $data;
    }

    protected function failedValidation(Validator $validator)
    {
        dd($validator->errors());
    }
}
