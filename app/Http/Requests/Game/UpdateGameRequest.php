<?php

namespace App\Http\Requests\Game;

use App\Models\Game;
use App\Http\Rules\ValidSeasonFormat;
use App\Http\Rules\ValidTeamId;
use Illuminate\Foundation\Http\FormRequest;

class UpdateGameRequest extends FormRequest
{
    public function authorize(): bool
    {
        $game = $this->route('game');
        if (!$game instanceof Game) {
            $game = Game::findOrFail($game);
        }
        return $this->user()->can('update', $game);
    }

    public function rules(): array
    {
        return [
            'home_team_id' => ['sometimes', new ValidTeamId()],
            'visitor_team_id' => ['sometimes', new ValidTeamId(), 'different:home_team_id'],
            'home_team_score' => ['sometimes', 'integer', 'min:0', 'max:300'],
            'visitor_team_score' => ['sometimes', 'integer', 'min:0', 'max:300'],
            'season' => ['sometimes', new ValidSeasonFormat()],
            'period' => ['sometimes', 'integer', 'min:0', 'max:20'],
            'status' => ['sometimes', 'string', 'max:50'],
            'time' => ['sometimes', 'nullable', 'string', 'max:20'],
            'postseason' => ['sometimes', 'boolean'],
            'game_date' => ['sometimes', 'date'],
        ];
    }
}
