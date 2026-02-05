<?php

namespace App\Http\Requests\Game;

use App\Http\Rules\ValidSeasonFormat;
use App\Http\Rules\ValidTeamId;

trait GameValidationRules
{
    /**
     * Base validation rules shared between Store and Update requests.
     */
    protected function baseRules(): array
    {
        return [
            'home_team_id' => [new ValidTeamId()],
            'visitor_team_id' => [new ValidTeamId(), 'different:home_team_id'],
            'home_team_score' => ['integer', 'min:0', 'max:300'],
            'visitor_team_score' => ['integer', 'min:0', 'max:300'],
            'season' => [new ValidSeasonFormat()],
            'period' => ['integer', 'min:0', 'max:20'],
            'status' => ['string', 'max:50'],
            'time' => ['nullable', 'string', 'max:20'],
            'postseason' => ['boolean'],
            'game_date' => ['date'],
        ];
    }

    /**
     * Custom validation messages.
     */
    protected function baseMessages(): array
    {
        return [
            'home_team_id.required' => 'Home team is required.',
            'visitor_team_id.required' => 'Visitor team is required.',
            'visitor_team_id.different' => 'Visitor team must be different from home team.',
            'home_team_score.integer' => 'Home team score must be an integer.',
            'visitor_team_score.integer' => 'Visitor team score must be an integer.',
            'season.required' => 'Season is required.',
            'status.required' => 'Status is required.',
            'game_date.required' => 'Game date is required.',
            'game_date.date' => 'Game date must be a valid date.',
        ];
    }
}
