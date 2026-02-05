<?php

namespace App\Http\Requests\Team;

use App\Models\Team;
use Illuminate\Foundation\Http\FormRequest;

class StoreTeamRequest extends FormRequest
{
    use TeamValidationRules;

    public function authorize(): bool
    {
        return $this->user()->can('create', Team::class);
    }

    public function rules(): array
    {
        return array_merge(
            array_map(fn($rules) => array_merge(['required'], $rules), $this->baseRules()),
            []
        );
    }

    public function messages(): array
    {
        return $this->baseMessages();
    }
}
