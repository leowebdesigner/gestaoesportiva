<?php

namespace App\Auth;

use Laravel\Sanctum\Contracts\HasAbilities;

class XAuthAccessToken implements HasAbilities
{
    public function __construct(
        private array $abilities = ['*']
    ) {}

    public function can($ability): bool
    {
        return in_array('*', $this->abilities)
            || in_array($ability, $this->abilities);
    }

    public function cant($ability): bool
    {
        return !$this->can($ability);
    }
}
