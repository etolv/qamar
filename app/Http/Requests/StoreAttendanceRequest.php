<?php

namespace App\Http\Requests;

use App\Enums\VacationStatusEnum;
use App\Models\Shift;
use App\Models\Vacation;
use App\Rules\UniqueEmployeeAttendance;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class StoreAttendanceRequest extends FormRequest
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
            'employee_id' => [
                'required',
                'exists:employees,id',
                new UniqueEmployeeAttendance($this->date)
            ],
            'date' => ['required', 'date_format:Y-m-d'],
            'start' => ['nullable', 'date_format:H:i', 'before:end'],
            'end' => ['nullable', 'date_format:H:i', 'after:start'],
        ];
    }

    public function afterValidation()
    {
        $data = $this->validated();
        return $data;
    }
}
