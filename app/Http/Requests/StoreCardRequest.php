<?php

namespace App\Http\Requests;

use App\Models\Branch;
use Illuminate\Foundation\Http\FormRequest;

class StoreCardRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'cardholder_name' => ['required', 'string', 'max:255'],
            'number' => ['required', 'string', 'max:255'],
            'iban' => ['required', 'string', 'max:255'],
            'cvv' => ['required', 'string', 'max:255'],
            'expiry' => ['required', 'date'],
        ];
    }

    public function afterValidation()
    {
        $data = $this->validated();
        $branch = auth()->user()->account?->branch;
        if (!$branch) {
            $branch = Branch::first();
        }
        $data['cardable_id'] = $branch->id;
        $data['cardable_type'] = Branch::class;
        return $data;
    }
}
