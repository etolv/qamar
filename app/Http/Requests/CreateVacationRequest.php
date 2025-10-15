<?php

namespace App\Http\Requests;

use App\Models\Employee;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class CreateVacationRequest extends FormRequest
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
            'employee_id' => ['nullable', 'exists:employees,id']
        ];
    }

    public function afterValidation()
    {
        $data = $this->validated();
        if (isset($data['employee_id']) && auth()->id() != $data['employee_id'] && auth()->user()->type instanceof Employee) {
            session()->flash('error', _t('You can only request vacation for yourself'));
            throw ValidationException::withMessages(['error' => _t("You can only request vacation for yourself")]);
        }
        return $data;
    }
}
