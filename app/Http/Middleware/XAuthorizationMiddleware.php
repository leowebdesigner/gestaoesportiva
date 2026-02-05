<?php

namespace App\Http\Middleware;

use App\Auth\XAuthAccessToken;
use App\Models\XAuthorizationToken;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class XAuthorizationMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header('X-Authorization');

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => __('messages.errors.not_authenticated'),
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
                'message' => __('messages.errors.unauthorized'),
            ], 401);
        }

        $xToken->update(['last_used_at' => now()]);

        $user = $xToken->user;
        Auth::setUser($user);
        $user->withAccessToken(new XAuthAccessToken($xToken->abilities ?? ['*']));

        return $next($request);
    }
}
