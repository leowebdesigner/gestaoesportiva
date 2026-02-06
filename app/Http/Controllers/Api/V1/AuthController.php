<?php

namespace App\Http\Controllers\Api\V1;

use App\Contracts\Services\AuthServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\RevokeXTokenRequest;
use App\Http\Resources\UserResource;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    use ApiResponse;

    public function __construct(
        private AuthServiceInterface $authService
    ) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->authService->register($request->validated());
        return $this->created([
            'user' => new UserResource($result['user']),
            'token' => $result['token'],
        ]);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->login($request->validated());
        return $this->success([
            'user' => new UserResource($result['user']),
            'token' => $result['token'],
        ]);
    }

    public function xLogin(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->loginForXAuth($request->validated());
        return $this->success([
            'user' => new UserResource($result['user']),
            'x_token' => $result['x_token']->plain_text_token,
            'expires_at' => $result['x_token']->expires_at?->toDateTimeString(),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        if ($request->attributes->has('x_auth_token') && $request->hasHeader('X-Authorization')) {
            $this->authService->revokeXToken($request->user(), $request->header('X-Authorization'));
        } else {
            $this->authService->logout($request->user());
        }

        return $this->success(
            ['logged_out' => true],
            __('messages.auth.logout_success')
        );
    }

    public function me(Request $request): JsonResponse
    {
        return $this->success(new UserResource($this->authService->me($request->user())));
    }

    public function createXToken(Request $request): JsonResponse
    {
        $token = $this->authService->createXToken($request->user(), $request->get('name', 'external'));

        return $this->created([
            'token' => $token->plain_text_token,
            'expires_at' => $token->expires_at?->toDateTimeString(),
        ]);
    }

    public function revokeXToken(RevokeXTokenRequest $request): JsonResponse
    {
        $revoked = $this->authService->revokeXToken($request->user(), $request->validated('token'));

        return $this->success(['revoked' => $revoked]);
    }

    public function registerExternal(RegisterRequest $request): JsonResponse
    {
        $result = $this->authService->registerExternal($request->validated());
        return $this->created([
            'user' => new UserResource($result['user']),
            'x_token' => $result['x_token']->plain_text_token,
            'expires_at' => $result['x_token']->expires_at?->toDateTimeString(),
        ]);
    }

    public function setExternalStatus(Request $request, \App\Models\User $user): JsonResponse
    {
        $this->authorize('setExternalStatus', $user);

        $isExternal = $request->boolean('is_external');
        $updatedUser = $this->authService->setExternalStatus($user, $isExternal);

        return $this->success([
            'user' => new UserResource($updatedUser),
            'message' => $isExternal
                ? 'User is now external (use X-Authorization).'
                : 'User is now internal (use Bearer Token).',
        ]);
    }
}
