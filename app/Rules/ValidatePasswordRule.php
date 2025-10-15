<?php

namespace App\Rules;

use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Hash;

class ValidatePasswordRule implements ValidationRule
{

    public function __construct(protected $phone) {}
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $hashed_password = Hash::make($value);
        if (!User::where('phone', $this->phone)->where('password', $hashed_password)->exists()) {
            $message = _t('Password does not match');
            $fail($message);
        }
    }
}
