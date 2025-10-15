<?php

namespace App\Http\Requests;

use App\Models\Notification;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class StoreNotificationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $rules = array();
        $rules['type'] = ['required', 'in:all,customers,drivers,employees,users'];
        $rules['name'] = ['required', 'string', 'max:255'];
        $rules['body'] = ['required', 'string', 'max:65000'];
        $rules['users'] = ['required_if:type,users', 'array'];
        $rules['users.*'] = ['numeric', 'exists:users,id'];
        return $rules;
    }

    public function afterValidation()
    {
        $data = $this->validated();
        $data['from_dashboard'] = true;
        $data['user_id'] = auth()->id();
        return $data;
    }
}
