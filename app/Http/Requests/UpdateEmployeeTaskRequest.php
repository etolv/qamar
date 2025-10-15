<?php

namespace App\Http\Requests;

use App\Enums\TaskStatusEnum;
use App\Models\EmployeeTask;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UpdateEmployeeTaskRequest extends FormRequest
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
            'status' => ['nullable', Rule::in(array_column(TaskStatusEnum::cases(), 'value'))],
        ];
    }

    public function afterValidation($id): array
    {
        $data = $this->validated();
        $employeeTaskExists = EmployeeTask::where('id', $id)->where(function ($query) {
            $query->where('employee_id', auth()->user()->type_id)
                ->orWhereRelation('task', 'user_id', auth()->id());
        })->exists();
        if (!$employeeTaskExists) {
            session()->flash('error', _t('Task not found'));
            throw ValidationException::withMessages(['error' => _t('Task not found')]);
        }
        return $data;
    }
}
