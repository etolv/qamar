<?php

namespace App\Http\Requests;

use App\Enums\RequestStatusEnum;
use App\Models\BookingEditRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBookingEditRequestRequest extends FormRequest
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
            'status' => ['required', Rule::in(array_column(RequestStatusEnum::cases(), 'value'))],
            'description' => ['required', 'string', 'max:65000'],
        ];
    }

    public function afterValidation($id)
    {
        $data = $this->validated();
        // $booking_edit = BookingEditRequest::find($id);
        return $data;
    }
}
