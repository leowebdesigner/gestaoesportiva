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
            'name.required' => 'O nome do time é obrigatório.',
            'name.max' => 'O nome do time não pode ter mais de 255 caracteres.',
            'city.required' => 'A cidade é obrigatória.',
            'abbreviation.required' => 'A abreviação é obrigatória.',
            'abbreviation.max' => 'A abreviação não pode ter mais de 10 caracteres.',
            'conference.required' => 'A conferência é obrigatória.',
            'division.required' => 'A divisão é obrigatória.',
            'full_name.required' => 'O nome completo é obrigatório.',
        ];
    }
}
