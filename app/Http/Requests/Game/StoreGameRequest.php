<?php

namespace App\Http\Requests\Game;

use App\Models\Game;
use Illuminate\Foundation\Http\FormRequest;

class StoreGameRequest extends FormRequest
{
    use GameValidationRules;

    public function authorize(): bool
    {
        return $this->user()->can('create', Game::class);
    }

    public function rules(): array
    {
        $rules = $this->baseRules();

        $rules['home_team_id'] = array_merge(['required'], $rules['home_team_id']);
        $rules['visitor_team_id'] = array_merge(['required'], $rules['visitor_team_id']);
        $rules['season'] = array_merge(['required'], $rules['season']);
        $rules['status'] = array_merge(['required'], $rules['status']);
        $rules['game_date'] = array_merge(['required'], $rules['game_date']);

        $rules['home_team_score'] = array_merge(['sometimes'], $rules['home_team_score']);
        $rules['visitor_team_score'] = array_merge(['sometimes'], $rules['visitor_team_score']);
        $rules['period'] = array_merge(['sometimes'], $rules['period']);
        $rules['time'] = array_merge(['sometimes'], $rules['time']);
        $rules['postseason'] = array_merge(['sometimes'], $rules['postseason']);

        return $rules;
    }

    public function messages(): array
    {
        return $this->baseMessages();
    }
}
