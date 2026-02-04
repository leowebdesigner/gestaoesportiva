<?php

namespace App\Contracts\Services;

use App\Models\User;
use App\Models\XAuthorizationToken;

interface AuthServiceInterface
{
    public function register(array $data): array;

    public function login(array $credentials): array;

    public function logout(User $user): void;

    public function me(User $user): User;

    public function createXToken(User $user, string $name = 'external'): XAuthorizationToken;

    public function revokeXToken(User $user, string $token): bool;
}
