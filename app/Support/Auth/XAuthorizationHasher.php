<?php

namespace App\Support\Auth;

class XAuthorizationHasher
{
    public static function hash(string $plain): string
    {
        return hash_hmac('sha256', $plain, (string) config('app.key'));
    }
}
