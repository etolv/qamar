<?php

namespace App\Http\Requests;

use App\Enums\ShiftTypeEnum;
use App\Enums\WeekDaysEnum;
use App\Models\Employee;
use App\Models\EmployeeShift;
use App\Models\Shift;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class StoreShiftRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'integer', Rule::in(array_column(ShiftTypeEnum::cases(), 'value'))],
            // 'holiday' => ['required', 'integer', Rule::in(array_column(WeekDaysEnum::cases(), 'value'))],
            // 'date' => ['required', 'string', 'regex:/^\d{4}-\d{2}-\d{2} to \d{4}-\d{2}-\d{2}$/'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i'],
            'start_break' => ['required_with:end_break', 'nullable', 'date_format:H:i', 'before:end_break', 'after:start_work', 'before:end_work'],
            'end_break' => ['required_with:start_break', 'nullable', 'date_format:H:i', 'after:start_break', 'before:end_work'],
            // 'employees' => ['nullable', 'array'],
            // 'employees.*' => ['integer', 'exists:employees,id'],
        ];
    }

    public function afterValidation()
    {
        $data = $this->validated();
        // [$from, $to] = explode(' to ', $data['date']);
        // $data['start'] = $from;
        // $data['end'] = $to;
        // unset($data['date']);
        // $shift_exists = Shift::where('type', $data['type'])
        //     ->where(function ($query) use ($data) {
        //         $query->where(function ($query) use ($data) {
        //             $query->whereBetween('start', [$data['start'], $data['end']]);
        //         })->orWhere(function ($query) use ($data) {
        //             $query->whereBetween('end', [$data['start'], $data['end']]);
        //         })->orWhere(function ($query) use ($data) {
        //             $query->where('start', '<=', $data['start'])
        //                 ->where('end', '>=', $data['end']);
        //         });
        //     })->exists();
        // if ($shift_exists) {
        //     session()->flash('error', _t('Shift within date range exists'));
        //     throw ValidationException::withMessages(['error' => _t('Shift within date range exists')]);
        // }
        // if (isset($data['employees']) && count($data['employees'])) {
        //     foreach ($data['employees'] as $employee_id) {
        //         $employee_shift_exists = EmployeeShift::whereHas('shift', function ($query) use ($data) {
        //             $query->where(function ($query) use ($data) {
        //                 $query->where(function ($query) use ($data) {
        //                     $query->whereBetween('start', [$data['start'], $data['end']]);
        //                 })->orWhere(function ($query) use ($data) {
        //                     $query->whereBetween('end', [$data['start'], $data['end']]);
        //                 })->orWhere(function ($query) use ($data) {
        //                     $query->where('start', '<=', $data['start'])
        //                         ->where('end', '>=', $data['end']);
        //                 });
        //             });
        //         })->where('employee_id', $employee_id)->exists();
        //         if ($employee_shift_exists) {
        //             $employee = Employee::find($employee_id);
        //             session()->flash('error', _t("Employee " . $employee->user->name . " has shift within the same date range"));
        //             throw ValidationException::withMessages(['error' => _t("Employee " . $employee->user->name . " has shift within the same date range")]);
        //         }
        //     }
        // }
        Carbon::now()->toIso8601ZuluString();
        return $data;
    }
}
