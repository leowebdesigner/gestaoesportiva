<?php

namespace App\Http\Requests\Player;

use App\Http\Rules\ValidTeamId;
use App\Models\Player;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePlayerRequest extends FormRequest
{
    public function authorize(): bool
    {
        $player = $this->route('player');
        if (!$player instanceof Player) {
            $player = Player::findOrFail($player);
        }
        return $this->user()->can('update', $player);
    }

    public function rules(): array
    {
        return [
            'first_name' => ['sometimes', 'string', 'max:255'],
            'last_name' => ['sometimes', 'string', 'max:255'],
            'team_id' => ['sometimes', 'nullable', new ValidTeamId()],
            'position' => ['sometimes', 'nullable', 'string', 'in:G,F,C,G-F,F-C'],
            'height' => ['sometimes', 'nullable', 'string', 'max:10'],
            'weight' => ['sometimes', 'nullable', 'string', 'max:10'],
            'jersey_number' => ['sometimes', 'nullable', 'string', 'max:10'],
            'college' => ['sometimes', 'nullable', 'string', 'max:255'],
            'country' => ['sometimes', 'nullable', 'string', 'max:100'],
            'draft_year' => ['sometimes', 'nullable', 'integer', 'min:1900', 'max:2100'],
            'draft_round' => ['sometimes', 'nullable', 'integer', 'min:1', 'max:20'],
            'draft_number' => ['sometimes', 'nullable', 'integer', 'min:1', 'max:300'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
