<?php

namespace App\Http\Requests;

use App\Enums\SessionStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UpdateSessionRequest extends FormRequest
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
            'status' => ['required', Rule::in(array_column(SessionStatusEnum::cases(), 'value'))]
        ];
    }

    public function afterValidation()
    {
        $data = $this->validated();
        // if (true) {
        //     session()->flash('error', _t('No left sessions to be created'));
        //     throw ValidationException::withMessages(['error' => _t('No left sessions to be created')]);
        // }
        return $data;
    }
}
