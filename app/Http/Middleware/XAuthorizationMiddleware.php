<?php

namespace App\Http\Middleware;

use App\Models\XAuthorizationToken;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class XAuthorizationMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header('X-Authorization');

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'X-Authorization header required',
            ], 401);
        }

        $xToken = XAuthorizationToken::query()
            ->where('token', hash('sha256', $token))
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->first();

        if (!$xToken) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired X-Authorization token',
            ], 401);
        }

        $xToken->update(['last_used_at' => now()]);

        $request->merge(['x_auth_user' => $xToken->user]);
        $request->merge(['x_auth_token' => $xToken]);

        return $next($request);
    }
}
