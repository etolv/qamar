<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCouponRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    return [
      'name' => ['required', 'string', 'max:255'],
      'code' => [
        'required', 'string', 'max:255',
        Rule::unique('coupons', 'code')->ignore($this->route('coupon')) // ← هنا المفتاح
      ],
      'from_date' => ['required', 'date', 'after_or_equal:today'],
      'to_date' => ['required', 'date', 'after_or_equal:from_date'],
      'discount' => ['required', 'numeric'],
      'services' => ['array'],
      'services.*' => ['exists:services,id'],
      'products' => ['array'],
      'products.*' => ['exists:products,id'],
    ];
  }

  public function afterValidation()
  {
    $data = $this->validated();
    unset($data['all_services'], $data['all_products']);
    return $data;
  }
}
