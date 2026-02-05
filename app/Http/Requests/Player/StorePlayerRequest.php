<?php

namespace App\Http\Requests\Player;

use App\Models\Player;
use Illuminate\Foundation\Http\FormRequest;

class StorePlayerRequest extends FormRequest
{
    use PlayerValidationRules;

    public function authorize(): bool
    {
        return $this->user()->can('create', Player::class);
    }

    public function rules(): array
    {
        return array_merge($this->baseRules(), [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
        ]);
    }

    public function messages(): array
    {
        return $this->baseMessages();
    }
}
