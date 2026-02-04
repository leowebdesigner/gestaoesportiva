<?php

namespace App\Http\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidSeasonFormat implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_numeric($value)) {
            $fail('A temporada deve ser um número válido.');
            return;
        }

        $year = (int) $value;

        if ($year < 1900 || $year > 2100) {
            $fail('A temporada deve estar entre 1900 e 2100.');
        }
    }
}
