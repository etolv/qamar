<?php

namespace App\Http\Requests;

use App\Models\Salary;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class StoreSalaryRequest extends FormRequest
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
            'salary_id' => ['nullable', 'exists:salaries,id'],
            'employee_id' => ['required', 'exists:employees,id'],
            'start_date' => ['required', 'date_format:Y-m-d'],
            'end_date' => ['date_format:Y-m-d', 'after:start_date'],
            'amount' => ['required', 'numeric'],
            'working_hours' => ['required', 'numeric'],
            'profit_percentage' => ['required', 'numeric', 'min:0'],
            'target' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function afterValidation()
    {
        $data = $this->validated();
        // if (Salary::where('employee_id', $data['employee_id'])->where('end_date', null)->exists()) {
        //     // session()->flash('error', _t('Employee already has a salary'));
        //     throw ValidationException::withMessages(['employee_id' => _t('Employee already has a salary')]);
        // }
        return $data;
    }
}
