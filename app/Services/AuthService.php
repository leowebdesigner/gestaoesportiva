<?php

namespace App\Services;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\Contracts\Services\AuthServiceInterface;
use App\Exceptions\UnauthorizedException;
use App\Models\User;
use App\Models\XAuthorizationToken;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthService implements AuthServiceInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {}

    public function register(array $data): array
    {
        $data['password'] = Hash::make($data['password']);
        $data['role'] = $data['role'] ?? 'user';

        $user = $this->userRepository->create($data);

        $token = $this->issueToken($user);

        return ['user' => $user, 'token' => $token];
    }

    public function login(array $credentials): array
    {
        if (!Auth::attempt($credentials)) {
            throw new UnauthorizedException(__('messages.auth.invalid_credentials'));
        }

        /** @var User $user */
        $user = $this->userRepository->findByEmail($credentials['email']);

        if (!$user) {
            throw new UnauthorizedException(__('messages.auth.user_not_found'));
        }

        $token = $this->issueToken($user);

        return ['user' => $user, 'token' => $token];
    }

    public function logout(User $user): void
    {
        $user->tokens()->delete();
    }

    public function me(User $user): User
    {
        return $user;
    }

    public function createXToken(User $user, string $name = 'external'): XAuthorizationToken
    {
        $plain = Str::random(60);
        $hashed = hash('sha256', $plain);

        $expiresAt = Carbon::now()->addDays((int) config('app.x_auth_token_expiration_days', 30));

        $token = $user->xAuthorizationTokens()->create([
            'token' => $hashed,
            'name' => $name,
            'abilities' => ['*'],
            'expires_at' => $expiresAt,
        ]);

        $token->plain_text_token = $plain;

        return $token;
    }

    public function revokeXToken(User $user, string $token): bool
    {
        $hashed = hash('sha256', $token);

        return (bool) $user->xAuthorizationTokens()
            ->where('token', $hashed)
            ->delete();
    }

    private function issueToken(User $user): string
    {
        if ($user->isAdministrator()) {
            return $user->createToken('admin-token', [
                'players:*',
                'teams:*',
                'games:*',
                'import:*',
            ])->plainTextToken;
        }

        return $user->createToken('api-token', [
            'players:read',
            'players:create',
            'players:update',
            'teams:read',
            'teams:create',
            'teams:update',
            'games:read',
            'games:create',
            'games:update',
        ])->plainTextToken;
    }
}
