<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RevokeXTokenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'token' => ['required', 'string', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'token.required' => 'Token is required.',
            'token.string' => 'Token must be a string.',
        ];
    }
}
