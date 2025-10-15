<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class FilterPackageRequest extends FormRequest
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
            'date' => ['nullable', 'string'],
        ];
    }

    public function afterValidation(): array
    {
        $data = $this->validated();
        if (isset($data['date']) && str_contains($data['date'], ' to ')) {
            [$data['start'], $data['end']] = explode(' to ', $data['date']);
        } else {
            [$data['start'], $data['end']] = [Carbon::now()->subMonths(1)->format('Y-m-d'), Carbon::now()->format('Y-m-d')];
        }
        $data['month'] = Carbon::parse($data['start'])->format('m');
        return $data;
    }
}
