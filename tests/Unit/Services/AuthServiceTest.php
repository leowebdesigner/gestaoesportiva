<?php

namespace Tests\Unit\Services;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\Exceptions\UnauthorizedException;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Mockery;
use Tests\TestCase;

class AuthServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_throws_for_invalid_credentials(): void
    {
        $repo = Mockery::mock(UserRepositoryInterface::class);
        Auth::shouldReceive('attempt')->once()->andReturn(false);

        $service = new AuthService($repo);

        $this->expectException(UnauthorizedException::class);
        $service->login(['email' => 'x@example.com', 'password' => 'wrong']);
    }

    public function test_me_returns_same_user(): void
    {
        $repo = Mockery::mock(UserRepositoryInterface::class);
        $service = new AuthService($repo);
        $user = User::factory()->create();

        $result = $service->me($user);

        $this->assertEquals($user->id, $result->id);
    }

    public function test_can_create_and_revoke_x_token(): void
    {
        $repo = Mockery::mock(UserRepositoryInterface::class);
        $service = new AuthService($repo);
        $user = User::factory()->create();

        $token = $service->createXToken($user, 'integration');

        $this->assertNotEmpty($token->plain_text_token);

        $revoked = $service->revokeXToken($user, $token->plain_text_token);

        $this->assertTrue($revoked);
    }
}
