<?php

namespace App\Http\Requests\Game;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGameRequest extends FormRequest
{
    use GameValidationRules;

    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('game'));
    }

    public function rules(): array
    {
        $rules = $this->baseRules();

        foreach (array_keys($rules) as $key) {
            $rules[$key] = array_merge(['sometimes'], $rules[$key]);
        }

        return $rules;
    }

    public function messages(): array
    {
        return $this->baseMessages();
    }
}
