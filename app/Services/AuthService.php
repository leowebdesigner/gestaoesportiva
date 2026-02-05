<?php

namespace App\Services;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\Contracts\Services\AuthServiceInterface;
use App\Enums\UserRole;
use App\Exceptions\UnauthorizedException;
use App\Models\User;
use App\Models\XAuthorizationToken;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AuthService implements AuthServiceInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {}

    public function register(array $data): array
    {
        $data['role'] = UserRole::USER->value;

        $user = $this->userRepository->create($data);

        $token = $this->issueToken($user);

        return ['user' => $user, 'token' => $token];
    }

    public function login(array $credentials): array
    {
        $user = $this->authenticateCredentials($credentials);

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

        $role = $user->role instanceof UserRole ? $user->role : UserRole::from($user->role);
        $expiresAt = Carbon::now()->addDays((int) config('app.x_auth_token_expiration_days', 30));

        $token = $user->xAuthorizationTokens()->create([
            'token' => $hashed,
            'name' => $name,
            'abilities' => $role->abilities(),
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

    public function loginForXAuth(array $credentials): array
    {
        $user = $this->authenticateCredentials($credentials);

        $xToken = $this->createXToken($user, 'external-login');

        return ['user' => $user, 'x_token' => $xToken];
    }

    private function authenticateCredentials(array $credentials): User
    {
        if (!Auth::attempt($credentials)) {
            throw new UnauthorizedException(__('messages.auth.invalid_credentials'));
        }

        /** @var User|null $user */
        $user = $this->userRepository->findByEmail($credentials['email']);

        if (!$user) {
            throw new UnauthorizedException(__('messages.auth.user_not_found'));
        }

        return $user;
    }

    private function issueToken(User $user): string
    {
        $role = $user->role instanceof UserRole ? $user->role : UserRole::from($user->role);

        return $user->createToken(
            $role === UserRole::ADMIN ? 'admin-token' : 'api-token',
            $role->abilities()
        )->plainTextToken;
    }
}
