<?php

namespace App\Http\Middleware;

use App\Support\Auth\XAuthorizationAuthenticator;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class XAuthorizationMiddleware
{
    public function __construct(
        private XAuthorizationAuthenticator $xAuthorizationAuthenticator
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header('X-Authorization');

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => __('messages.errors.not_authenticated'),
            ], 401);
        }

        $xToken = $this->xAuthorizationAuthenticator->authenticate($token);
        $this->xAuthorizationAuthenticator->attachAuthenticatedUser($xToken);
        $request->attributes->set('x_auth_token', $xToken);

        return $next($request);
    }
}
