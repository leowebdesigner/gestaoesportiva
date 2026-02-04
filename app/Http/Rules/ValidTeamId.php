<?php

namespace App\Http\Rules;

use App\Models\Team;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidTeamId implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $team = Team::query()->where('id', $value)->whereNull('deleted_at')->first();

        if (!$team) {
            $fail('O time selecionado não existe ou foi removido.');
        }
    }
}
