<?php

namespace App\Http\Requests\Team;

use App\Models\Team;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTeamRequest extends FormRequest
{
    public function authorize(): bool
    {
        $team = $this->route('team');
        if (!$team instanceof Team) {
            $team = Team::findOrFail($team);
        }
        return $this->user()->can('update', $team);
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'city' => ['sometimes', 'string', 'max:255'],
            'abbreviation' => ['sometimes', 'string', 'max:10'],
            'conference' => ['sometimes', 'string', 'max:50'],
            'division' => ['sometimes', 'string', 'max:100'],
            'full_name' => ['sometimes', 'string', 'max:255'],
        ];
    }
}
