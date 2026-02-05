<?php

namespace App\Http\Controllers\Api\V1;

use App\Contracts\Services\AuthServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
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

    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());
        return $this->noContent();
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

    public function revokeXToken(Request $request): JsonResponse
    {
        $token = (string) $request->input('token', '');
        if ($token === '') {
            return $this->error('Token é obrigatório.', 422);
        }

        $revoked = $this->authService->revokeXToken($request->user(), $token);

        return $this->success(['revoked' => $revoked]);
    }
}
