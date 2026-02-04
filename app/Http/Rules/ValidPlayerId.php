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
            $fail('O jogador selecionado não existe ou foi removido.');
        }
    }
}
