<?php

namespace App\Support\Auth;

use App\Auth\XAuthAccessToken;
use App\Models\XAuthorizationToken;
use App\Support\Auth\XAuthorizationHasher;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Auth;

class XAuthorizationAuthenticator
{
    public function authenticate(string $plainToken): XAuthorizationToken
    {
        $xToken = XAuthorizationToken::query()
            ->with('user')
            ->where('token', XAuthorizationHasher::hash($plainToken))
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->first();

        if (!$xToken || !$xToken->user) {
            throw new AuthenticationException(__('messages.errors.unauthorized'));
        }

        $xToken->update(['last_used_at' => now()]);

        return $xToken;
    }

    public function attachAuthenticatedUser(XAuthorizationToken $xToken): void
    {
        $user = $xToken->user;

        Auth::setUser($user);
        $user->withAccessToken(new XAuthAccessToken($xToken->abilities ?? ['*']));
    }
}
