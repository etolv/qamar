<?php

namespace App\Http\Requests;

use App\Enums\SessionStatusEnum;
use App\Models\OrderService;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class StoreSessionRequest extends FormRequest
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
            'order_service_id' => ['required', 'exists:order_service,id'],
            'employee_id' => ['nullable', 'exists:employees,id'],
            'date' => ['required', 'date'],
            'status' => ['required', Rule::in(array_column(SessionStatusEnum::cases(), 'value'))]
        ];
    }

    public function afterValidation()
    {
        $data = $this->validated();
        $order_service = OrderService::find($data['order_service_id']);
        if ($order_service->due_date < Carbon::now()) {
            session()->flash('error', _t('Session Due date is over'));
            throw ValidationException::withMessages(['error' => _t('Session Due date is over')]);
        }
        if ($order_service->sessions()->count() >= $order_service->session_count) {
            session()->flash('error', _t('No left sessions to be created'));
            throw ValidationException::withMessages(['error' => _t('No left sessions to be created')]);
        }
        return $data;
    }
}
