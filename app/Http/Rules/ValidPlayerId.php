<?php

namespace App\Http\Rules;

use App\Models\Player;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidPlayerId implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $player = Player::query()->where('id', $value)->whereNull('deleted_at')->first();

        if (!$player) {
            $fail('Selected player does not exist or has been removed.');
        }
    }
}
