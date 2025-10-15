<?php

namespace App\Http\Requests;

use App\Enums\VacationStatusEnum;
use App\Models\Vacation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UpdateVacationStatusRequest extends FormRequest
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
            'status' => ['required', 'string',  Rule::in(array_column(VacationStatusEnum::cases(), 'name'))],
            'reject_reason' => ['nullable', 'string']
        ];
    }

    public function afterValidation($id)
    {
        $data = $this->validated();
        $vacation = Vacation::find($id);
        if (in_array($data['status'], ['APPROVED', 'DECLINED'])) {
            if (!in_array($vacation->status, [VacationStatusEnum::PENDING_REPORT, VacationStatusEnum::IN_REVIEW])) {
                session()->flash('error', _t('Vacation Status can not be changed'));
                throw ValidationException::withMessages(['error' => _t('Vacation Status can not be changed')]);
            }
        } else if ($data['status'] == 'CANCELLED') {
            if (in_array($vacation->status, [VacationStatusEnum::CANCELED, VacationStatusEnum::DECLINED])) {
                session()->flash('error', _t('Vacation Status can not be changed'));
                throw ValidationException::withMessages(['error' => _t('Vacation Status can not be changed')]);
            }
        } else if ($data['status'] == 'PENDING_REPORT') {
            if ($vacation->status != VacationStatusEnum::IN_REVIEW) {
                session()->flash('error', _t('Vacation Status can not be changed'));
                throw ValidationException::withMessages(['error' => _t('Vacation Status can not be changed')]);
            }
        }
        $data['status'] = VacationStatusEnum::fromName($data['status'])->value;
        return $data;
    }

    public function failedValidation($validator)
    {
        if ($validator->errors()->first()) {
            session()->flash('error', $validator->errors()->first());
            throw new ValidationException($validator);
        }
    }
}
