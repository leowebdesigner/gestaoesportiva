<?php

namespace App\Http\Requests\Import;

use Illuminate\Foundation\Http\FormRequest;

class ImportGamesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'season' => ['nullable', 'integer', 'min:1946', 'max:2100'],
            'team_id' => ['nullable', 'integer', 'min:1'],
            'playoffs' => ['nullable', 'boolean'],
        ];
    }
}
