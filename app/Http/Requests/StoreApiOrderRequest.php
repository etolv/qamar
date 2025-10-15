<?php

namespace App\Http\Requests;

use App\Enums\PaymentTypeEnum;
use App\Models\Branch;
use App\Models\Brand;
use App\Models\Coupon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreApiOrderRequest extends FormRequest
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
            'branch_id' => ['nullable', 'exists:branches,id'],
            'coupon_code' => ['nullable', 'exists:coupons,code'],
            'description' => ['nullable', 'string', 'max:65000'],
            'stocks' => ['nullable', 'array'],
            'stocks.*.id' => ['required', 'exists:stocks,id'],
            'stocks.*.quantity' => ['required', 'numeric'],
            'payment_type' => ['required', Rule::in(array_column(PaymentTypeEnum::cases(), 'name'))],
        ];
    }

    public function afterValidation()
    {
        $data = $this->validated();
        $data['customer_id'] = auth()->user()->type_id;
        $data['payment_type'] = PaymentTypeEnum::fromName($data['payment_type'])->value;
        if (isset($data['coupon_code'])) {
            $data['coupon_id'] = Coupon::where('code', $data['coupon_code'])->first()?->id;
            unset($data['coupon_code']);
        }
        if (!isset($data['branch_id'])) {
            $data['branch_id'] = auth()->user()->account->branch_id ?? Branch::first()->id;
        }
        $data['is_gift'] = false;
        $data['is_mobile'] = true;
        $data['tax_included'] = true;
        return $data;
    }
}
