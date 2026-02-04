<?php

namespace App\Http\Requests\Player;

use App\Models\Player;
use App\Http\Rules\ValidTeamId;
use Illuminate\Foundation\Http\FormRequest;

class StorePlayerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Player::class);
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
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

    public function messages(): array
    {
        return [
            'first_name.required' => 'O primeiro nome é obrigatório.',
            'last_name.required' => 'O último nome é obrigatório.',
            'position.in' => 'A posição deve ser uma das seguintes: G, F, C, G-F, F-C.',
        ];
    }
}
