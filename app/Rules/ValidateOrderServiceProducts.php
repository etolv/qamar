<?php

namespace App\Rules;

use App\Services\ServiceService;
use App\Services\StockService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidateOrderServiceProducts implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $service = resolve(ServiceService::class)->show($value);
        foreach ($service->productServices as $productService) {
            $stock_model = resolve(StockService::class)->product_stock($productService->product_id);
            if (!$stock_model || ($stock_model && $stock_model->quantity < $productService->quantity)) {
                $fail("Product stock for service $service->name is not enough");
            }
        }
    }
}
