<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class XLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_external_user_can_login_and_receive_x_authorization_token(): void
    {
        User::factory()->create([
            'email' => 'external@example.com',
            'password' => Hash::make('password123'),
            'is_external' => true,
        ]);

        $response = $this->postJson('/api/v1/auth/x-login', [
            'email' => 'external@example.com',
            'password' => 'password123',
        ]);

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => ['user', 'x_token', 'expires_at'],
        ]);
    }

    public function test_internal_user_cannot_login_via_x_login(): void
    {
        User::factory()->create([
            'email' => 'internal@example.com',
            'password' => Hash::make('password123'),
            'is_external' => false,
        ]);

        $response = $this->postJson('/api/v1/auth/x-login', [
            'email' => 'internal@example.com',
            'password' => 'password123',
        ]);

        $response->assertUnauthorized();
        $response->assertJsonPath('message', 'Internal users must authenticate via /auth/login.');
    }
}
