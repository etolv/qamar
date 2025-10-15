<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UniqueValueRule implements ValidationRule
{

    public function __construct(protected mixed $id, protected string $model) {}
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $exists = $this->model::where('id', '!=', $this->id)
            ->whereHas('user', function ($query) use ($attribute, $value) {
                $query->withTrashed()->where($attribute, $value);
            })->exists();
        if ($exists) {
            $message = _t('Value :attribute already exists');
            $fail($message);
        }
    }
}
