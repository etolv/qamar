<?php

namespace App\Rules;

use App\Models\Trip;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UniqueTripableIdRule implements ValidationRule
{

    public function __construct(private $type, private $trip_id = null) {}
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($this->type != 'general') {
            $type = "App\\Models\\" . ucfirst($this->type);
            $trip = Trip::when($this->trip_id, function ($query) {
                $query->where('id', '!=', $this->trip_id);
            })->where('tripable_id', $value)->where('tripable_type', $type)->exists();
            if ($trip) {
                $fail("The trip for this {$this->type} has already been taken.");
            }
        }
    }
}
