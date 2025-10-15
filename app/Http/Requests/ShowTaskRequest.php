<?php

namespace App\Http\Requests;

use App\Models\Task;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class ShowTaskRequest extends FormRequest
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

    public function  afterValidation($id)
    {
        $data = $this->validated();
        $data['employee_id'] = auth()->user()->type_id;
        $task_exists = Task::where('id', $id)->where(function ($query) use ($data) {
            $query->where('user_id', auth()->id())
                ->orWhereRelation('employeeTasks', 'employee_id', $data['employee_id']);
        })->exists();
        if (!$task_exists) {
            throw ValidationException::withMessages(['error' => _t('Task not found')]);
        }
        return $data;
    }
}
