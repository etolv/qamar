<?php

namespace App\Http\Requests;

use App\Enums\CashFlowStatusEnum;
use App\Enums\CashFlowTypeEnum;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StoreCashFlowRequest extends FormRequest
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
            'amount' => ['required', 'numeric'],
            'split_months_count' => ['nullable', 'numeric', 'min:0'],
            'type' => ['required', 'numeric', Rule::in(array_column(CashFlowTypeEnum::cases(), 'value'))],
            'employee_id' => ['required_if:type,' . CashFlowTypeEnum::ADVANCE->value, 'numeric', 'exists:employees,id'],
            'due_date' => ['nullable', 'date', 'after:today'],
            'reason' => ['nullable', 'string', 'max:65000'],
        ];
    }

    public function afterValidation()
    {
        $data = $this->validated();
        $data['status'] = CashFlowStatusEnum::PAID->value;
        $data['due_date'] = isset($data['due_date']) ? $data['due_date'] : Carbon::now()->endOfMonth()->format('Y-m-d');
        if (isset($data['employee_id'])) {
            $data['flowable_id'] = $data['employee_id'];
            $data['flowable_type'] = Employee::class;
        } else {
            $user = Auth::user();
            $data['flowable_id'] = $user->type_id;
            $data['flowable_type'] = $user->type_type;
        }
        if ($data['type'] != CashFlowTypeEnum::EXPENSE->value) {
            $data['status'] = CashFlowStatusEnum::POSTPONED->value;
        }
        isset($data['split_months_count']) ? $data['split_months_count'] = $data['split_months_count'] : $data['split_months_count'] = 1;
        return $data;
    }
}
