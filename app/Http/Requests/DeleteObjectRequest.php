<?php

namespace App\Http\Requests;

use App\Enums\DeleteableModelEnum;
use App\Enums\DeleteActionsEnum;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

use Illuminate\Foundation\Http\FormRequest;

class DeleteObjectRequest extends FormRequest
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

    public function prepareForValidation()
    {
        $this->merge([
            'objectId' => $this->objectId,
            'objectType' => $this->objectType,
            'actionType' => $this->actionType,
            'withTrashed' => $this->withTrashed,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $modelClass = "App\\Models\\" . $this->objectType;
        $table_name = (new $modelClass)->getTable();
        return [
            'objectType' => ['required', Rule::in(array_column(DeleteableModelEnum::cases(), 'name'))],
            'objectId' => ['required', Rule::exists($table_name, 'id')],
            'actionType' => ['required', 'string', Rule::in(array_column(DeleteActionsEnum::cases(), 'name'))],
            'withTrashed' => ['nullable', 'boolean']
        ];
    }
}
