<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class XLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_and_receive_x_authorization_token(): void
    {
        User::factory()->create([
            'email' => 'external@example.com',
            'password' => Hash::make('password123'),
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
}
