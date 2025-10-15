<?php

namespace App\Http\Requests;

use App\Models\EmployeeShift;
use App\Models\Shift;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class DestroyEmployeeShiftRequest extends FormRequest
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
        $employee_shift = EmployeeShift::find($id);
        if ($employee_shift->shift->attendances()->where('employee_id', $employee_shift->employee_id)->exists()) {
            session()->flash('error', _t('Employee already has attendance in this shift'));
            throw ValidationException::withMessages(['employee_id' => _t('Employee already has attendance in this shift')]);
        }
        return $data;
    }
}
