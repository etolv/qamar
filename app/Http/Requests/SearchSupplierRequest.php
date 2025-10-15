<?php

namespace App\Http\Requests;

use App\Enums\DepartmentEnum;
use App\Enums\SupplierTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SearchSupplierRequest extends FormRequest
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
            'q' => ['string'],
            'department' => ['nullable', Rule::in(array_column(DepartmentEnum::cases(), 'value'))],
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
        ];
    }

    public function afterValidation()
    {
        $data = $this->validated();
        if (isset($data['department'])) {
            $data['type'] = $data['department'] == DepartmentEnum::CAFETERIA->value ? [SupplierTypeEnum::CAFETERIA->value] : [SupplierTypeEnum::ONLINE->value, SupplierTypeEnum::PHYSICAL->value];
        }
        return $data;
    }
}
