<?php

namespace App\Http\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UniqueExternalId implements ValidationRule
{
    public function __construct(
        private string $model,
        private ?string $ignoreId = null
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $query = $this->model::where('external_id', $value);

        if ($this->ignoreId) {
            $query->where('id', '!=', $this->ignoreId);
        }

        if ($query->exists()) {
            $fail('This external ID is already in use.');
        }
    }
}
