<?php

namespace App\Http\Requests;

use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class CompleteOrderRequest extends FormRequest
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
        $order = Order::find($id);
        if (!in_array($order->status->name, ['PENDING', 'STARTED'])) {
            session()->flash('error', _t('Order Status can not be changed'));
            throw ValidationException::withMessages(['error' => _t('Order Status can not be changed')]);
        }
        return $data;
    }
}
