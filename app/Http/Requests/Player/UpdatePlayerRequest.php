<?php

namespace App\Http\Requests\Player;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePlayerRequest extends FormRequest
{
    use PlayerValidationRules;

    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('player'));
    }

    public function rules(): array
    {
        return array_merge($this->baseRules(), [
            'first_name' => ['sometimes', 'string', 'max:255'],
            'last_name' => ['sometimes', 'string', 'max:255'],
        ]);
    }

    public function messages(): array
    {
        return $this->baseMessages();
    }
}
