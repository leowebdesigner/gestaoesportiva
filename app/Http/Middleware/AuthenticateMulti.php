<?php

namespace App\Http\Middleware;

use App\Auth\XAuthAccessToken;
use App\Models\XAuthorizationToken;
use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateMulti
{
    public function handle(Request $request, Closure $next): Response
    {
        // 1) Try X-Authorization header (external systems)
        if ($request->hasHeader('X-Authorization')) {
            return $this->authenticateViaXAuth($request, $next);
        }

        // 2) Try Bearer token (Sanctum - frontend)
        if ($request->bearerToken()) {
            return $this->authenticateViaSanctum($request, $next);
        }

        throw new AuthenticationException(__('messages.errors.not_authenticated'));
    }

    private function authenticateViaXAuth(Request $request, Closure $next): Response
    {
        $token = $request->header('X-Authorization');

        $xToken = XAuthorizationToken::query()
            ->where('token', hash('sha256', $token))
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->first();

        if (!$xToken) {
            throw new AuthenticationException(__('messages.errors.unauthorized'));
        }

        $xToken->update(['last_used_at' => now()]);

        $user = $xToken->user;
        Auth::setUser($user);
        $user->withAccessToken(new XAuthAccessToken($xToken->abilities ?? ['*']));

        return $next($request);
    }

    private function authenticateViaSanctum(Request $request, Closure $next): Response
    {
        $middleware = new \Laravel\Sanctum\Http\Middleware\CheckForAnyAbility();

        // First authenticate via Sanctum guard
        return app(\Illuminate\Auth\Middleware\Authenticate::class)
            ->handle($request, $next, 'sanctum');
    }
}
