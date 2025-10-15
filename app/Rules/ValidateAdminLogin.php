<?php

namespace App\Rules;

use App\Models\Customer;
use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidateAdminLogin implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $user = User::where('phone', $value)->first();
        if (!$user) {
            $fail(_t("User not found"));
        } else if ($user?->account instanceof Customer) {
            $fail(_t("Only admins allowed to login"));
        }
    }
}
