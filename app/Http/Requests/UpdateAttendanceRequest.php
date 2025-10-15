<?php

namespace App\Http\Requests;

use App\Enums\OverTimeStatusEnum;
use App\Enums\VacationStatusEnum;
use App\Models\Shift;
use App\Models\Vacation;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAttendanceRequest extends FormRequest
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
            'employee_id' => ['nullable', 'exists:employees,id'],
            'overtime_status' => ['nullable', Rule::in(array_column(OverTimeStatusEnum::cases(), 'value'))],
            'date' => ['nullable', 'date_format:Y-m-d'],
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
