<?php

namespace App\Http\Middleware;

use App\Support\Auth\XAuthorizationAuthenticator;
use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateMulti
{
    public function __construct(
        private XAuthorizationAuthenticator $xAuthorizationAuthenticator
    ) {}

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

        // 3) Already authenticated via Sanctum guard (e.g. actingAs in tests)
        if (Auth::guard('sanctum')->check()) {
            return $next($request);
        }

        throw new AuthenticationException(__('messages.errors.not_authenticated'));
    }

    private function authenticateViaXAuth(Request $request, Closure $next): Response
    {
        $token = $request->header('X-Authorization');
        $xToken = $this->xAuthorizationAuthenticator->authenticate($token);
        $this->xAuthorizationAuthenticator->attachAuthenticatedUser($xToken);
        $request->attributes->set('x_auth_token', $xToken);

        return $next($request);
    }

    private function authenticateViaSanctum(Request $request, Closure $next): Response
    {
        return app(\Illuminate\Auth\Middleware\Authenticate::class)
            ->handle($request, $next, 'sanctum');
    }
}
