<?php

namespace App\Http\Requests\Player;

trait PlayerValidationRules
{
    /**
     * Base validation rules shared between Store and Update requests.
     */
    protected function baseRules(): array
    {
        return [
            'team_id' => ['sometimes', 'nullable', new \App\Http\Rules\ValidTeamId()],
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

    /**
     * Custom validation messages.
     */
    protected function baseMessages(): array
    {
        return [
            'first_name.required' => 'O primeiro nome é obrigatório.',
            'first_name.string' => 'O primeiro nome deve ser um texto.',
            'first_name.max' => 'O primeiro nome não pode ter mais de 255 caracteres.',
            'last_name.required' => 'O último nome é obrigatório.',
            'last_name.string' => 'O último nome deve ser um texto.',
            'last_name.max' => 'O último nome não pode ter mais de 255 caracteres.',
            'position.in' => 'A posição deve ser uma das seguintes: G, F, C, G-F, F-C.',
            'draft_year.integer' => 'O ano do draft deve ser um número inteiro.',
            'draft_year.min' => 'O ano do draft deve ser no mínimo 1900.',
            'draft_year.max' => 'O ano do draft deve ser no máximo 2100.',
        ];
    }
}
