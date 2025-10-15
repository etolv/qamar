<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderServicePostPoneRequest extends FormRequest
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
            'services' => 'required|array|min:1',
            'service.*.date' => 'required|date',
            'service.*.postpone' => 'required|date',
            'service.*.quantity' => 'required|numeric|min:1',
        ];
    }

    public function afterValidation()
    {
        $data = $this->validated();
        $data['order_services'] = array();
        foreach ($data['services'] as $index => $service) {
            if (isset($service['postpone'])) {
                $data['order_services'][$index]['order_service_id'] = $index;
                $data['order_services'][$index]['due_date'] = $service['date'];
                $data['order_services'][$index]['quantity'] = $service['quantity'];
            }
        }
        return $data;
    }
}
