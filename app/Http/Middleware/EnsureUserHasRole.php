<?php

namespace App\Http\Middleware;

use Closure;
use BackedEnum;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();
        $userRole = $user?->role;
        $userRoleValue = $userRole instanceof BackedEnum ? $userRole->value : $userRole;

        if (!$user || $userRoleValue !== $role) {
            return response()->json([
                'success' => false,
                'message' => __('messages.errors.unauthorized_action'),
            ], 403);
        }

        return $next($request);
    }
}
