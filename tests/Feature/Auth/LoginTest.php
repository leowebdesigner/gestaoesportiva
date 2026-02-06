<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login(): void
    {
        User::factory()->create([
            'email' => 'john@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'john@example.com',
            'password' => 'password123',
        ]);

        $response->assertOk();
        $response->assertJsonStructure(['data' => ['token', 'user']]);
    }

    public function test_external_user_cannot_login_via_normal_login(): void
    {
        User::factory()->create([
            'email' => 'external@example.com',
            'password' => Hash::make('password123'),
            'is_external' => true,
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'external@example.com',
            'password' => 'password123',
        ]);

        $response->assertUnauthorized();
        $response->assertJsonPath('message', 'External users must authenticate via /auth/x-login.');
    }
}
