<?php

namespace App\Http\Requests;

use App\Models\Notification;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class ShowNotificationRequest extends FormRequest
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
        $data['user_id'] = auth()->id();
        if (Notification::where('id', $id)->whereRelation('notificationUsers', 'user_id', $data['user_id'])->doesntExist()) {
            throw ValidationException::withMessages(['error' => _t('Notification not found')]);
        }
        return $data;
    }
}
