<?php

namespace App\Http\Requests\Team;

trait TeamValidationRules
{
    /**
     * Base validation rules shared between Store and Update requests.
     */
    protected function baseRules(): array
    {
        return [
            'name' => ['string', 'max:255'],
            'city' => ['string', 'max:255'],
            'abbreviation' => ['string', 'max:10'],
            'conference' => ['string', 'max:50'],
            'division' => ['string', 'max:100'],
            'full_name' => ['string', 'max:255'],
        ];
    }

    /**
     * Custom validation messages.
     */
    protected function baseMessages(): array
    {
        return [
            'name.required' => 'Team name is required.',
            'name.max' => 'Team name may not be greater than 255 characters.',
            'city.required' => 'City is required.',
            'abbreviation.required' => 'Abbreviation is required.',
            'abbreviation.max' => 'Abbreviation may not be greater than 10 characters.',
            'conference.required' => 'Conference is required.',
            'division.required' => 'Division is required.',
            'full_name.required' => 'Full name is required.',
        ];
    }
}
