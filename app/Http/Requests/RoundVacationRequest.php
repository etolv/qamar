<?php

namespace App\Http\Requests;

use App\Enums\VacationStatusEnum;
use App\Enums\VacationTypeEnum;
use App\Models\Employee;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RoundVacationRequest extends FormRequest
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
            'type' => ['required', Rule::enum(VacationTypeEnum::class)],
            'days' => ['required', 'min:0.1', 'max:' . Employee::find($this->employee)->remaining_vacation_days]
        ];
    }

    public function afterValidation($employee_id)
    {
        $data = $this->validated();
        $data['employees'] = [$employee_id];
        $data['is_hourly'] = false;
        $data['status'] = VacationStatusEnum::PENDING->value;
        return $data;
    }
}
