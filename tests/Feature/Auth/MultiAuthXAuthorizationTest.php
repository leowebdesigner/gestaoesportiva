<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MultiAuthXAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_access_me_with_x_authorization_header(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $tokenResponse = $this->postJson('/api/v1/auth/x-token', [
            'name' => 'integration',
        ]);

        $token = $tokenResponse->json('data.token');

        $response = $this->withHeaders([
            'X-Authorization' => $token,
        ])->getJson('/api/v1/auth/me');

        $response->assertOk();
        $response->assertJsonPath('data.email', $user->email);
    }

    public function test_logout_revokes_x_authorization_token_used_in_request(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $tokenResponse = $this->postJson('/api/v1/auth/x-token', [
            'name' => 'integration',
        ]);

        $token = $tokenResponse->json('data.token');

        $logoutResponse = $this->withHeaders([
            'X-Authorization' => $token,
        ])->postJson('/api/v1/auth/logout');

        $logoutResponse->assertOk();
        $logoutResponse->assertJsonPath('message', 'Logout successful.');
        $logoutResponse->assertJsonPath('data.logged_out', true);

        $meResponse = $this->withHeaders([
            'X-Authorization' => $token,
        ])->getJson('/api/v1/auth/me');

        $meResponse->assertUnauthorized();
    }
}
