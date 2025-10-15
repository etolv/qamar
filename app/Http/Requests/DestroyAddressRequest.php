<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class DestroyAddressRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function prepareForValidation(): void
    {
        $this->merge([
            'customer_id' => auth()->user()->type_id,
            'address_id' => $this->address
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'customer_id' => ['required', 'exists:customers,id'],
            'address_id' => [
                'required',
                Rule::exists('addresses', 'id')->where(function ($query) {
                    $query->where('customer_id', $this->customer_id)
                        ->whereNotExists(function ($sub) {
                            $sub->select(DB::raw(1))
                                ->from('bookings')
                                ->whereColumn('bookings.address_id', 'addresses.id');
                        });
                }),
            ],
        ];
    }
}
