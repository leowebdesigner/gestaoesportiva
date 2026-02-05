<?php

namespace App\Http\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidSeasonFormat implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_numeric($value)) {
            $fail('Season must be a valid number.');
            return;
        }

        $year = (int) $value;

        if ($year < 1900 || $year > 2100) {
            $fail('Season must be between 1900 and 2100.');
        }
    }
}
