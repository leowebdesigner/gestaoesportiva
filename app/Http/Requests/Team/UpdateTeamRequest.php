<?php

namespace App\Http\Requests\Team;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTeamRequest extends FormRequest
{
    use TeamValidationRules;

    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('team'));
    }

    public function rules(): array
    {
        return array_merge(
            array_map(fn($rules) => array_merge(['sometimes'], $rules), $this->baseRules()),
            []
        );
    }

    public function messages(): array
    {
        return $this->baseMessages();
    }
}
