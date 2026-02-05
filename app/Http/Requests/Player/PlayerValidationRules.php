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
            'first_name.required' => 'First name is required.',
            'first_name.string' => 'First name must be a string.',
            'first_name.max' => 'First name may not be greater than 255 characters.',
            'last_name.required' => 'Last name is required.',
            'last_name.string' => 'Last name must be a string.',
            'last_name.max' => 'Last name may not be greater than 255 characters.',
            'position.in' => 'Position must be one of: G, F, C, G-F, F-C.',
            'draft_year.integer' => 'Draft year must be an integer.',
            'draft_year.min' => 'Draft year must be at least 1900.',
            'draft_year.max' => 'Draft year must not be greater than 2100.',
        ];
    }
}
