<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case USER = 'user';

    public function abilities(): array
    {
        return match ($this) {
            self::ADMIN => [
                'players:*',
                'teams:*',
                'games:*',
                'import:*',
            ],
            self::USER => [
                'players:read',
                'players:create',
                'players:update',
                'teams:read',
                'teams:create',
                'teams:update',
                'games:read',
                'games:create',
                'games:update',
            ],
        };
    }
}
