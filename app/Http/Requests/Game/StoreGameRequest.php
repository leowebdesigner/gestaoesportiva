<?php

namespace App\Http\Requests\Game;

use App\Models\Game;
use App\Http\Rules\ValidSeasonFormat;
use App\Http\Rules\ValidTeamId;
use Illuminate\Foundation\Http\FormRequest;

class StoreGameRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Game::class);
    }

    public function rules(): array
    {
        return [
            'home_team_id' => ['required', new ValidTeamId()],
            'visitor_team_id' => ['required', new ValidTeamId(), 'different:home_team_id'],
            'home_team_score' => ['sometimes', 'integer', 'min:0', 'max:300'],
            'visitor_team_score' => ['sometimes', 'integer', 'min:0', 'max:300'],
            'season' => ['required', new ValidSeasonFormat()],
            'period' => ['sometimes', 'integer', 'min:0', 'max:20'],
            'status' => ['required', 'string', 'max:50'],
            'time' => ['sometimes', 'nullable', 'string', 'max:20'],
            'postseason' => ['sometimes', 'boolean'],
            'game_date' => ['required', 'date'],
        ];
    }
}
