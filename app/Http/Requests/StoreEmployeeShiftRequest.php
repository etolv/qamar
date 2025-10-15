<?php

namespace App\Http\Requests;

use App\Models\EmployeeShift;
use App\Models\Shift;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class StoreEmployeeShiftRequest extends FormRequest
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
            'employees' => ['required', 'array'],
            'employees.*' => ['required', 'exists:employees,id'],
            'shift_id' => ['required', 'exists:shifts,id'],
            'days' => ['required', 'string'],
        ];
    }

    public function afterValidation()
    {
        $data = $this->validated();
        // $data['days'] = explode(',', $data['days']);
        $days = collect(explode(',', $data['days']))
            ->map(fn($day) => Carbon::parse($day)->format('Y-m-d'))
            ->all();
        $employee_shift_exists = EmployeeShift::whereIn('employee_id', $data['employees'])
            ->whereIn('date', $days)->exists();
        if ($employee_shift_exists) {
            session()->flash('error', _t('Employees has shift in this period'));
            throw ValidationException::withMessages(['employees' => _t('Employees has shift in this period')]);
        }
        $data['employee_shifts'] = collect($data['employees'])
            ->crossJoin($days)
            ->map(fn($combination) => [
                'employee_id' => $combination[0],
                'shift_id' => $data['shift_id'],
                'date' => $combination[1]
            ])
            ->all();
        return $data;
    }
}
