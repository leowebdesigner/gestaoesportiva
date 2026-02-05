<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RevokeXAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_revoke_x_token(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['players:read']);

        $create = $this->postJson('/api/v1/auth/x-token', ['name' => 'integration']);
        $token = $create->json('data.token');

        $response = $this->deleteJson('/api/v1/auth/x-token', ['token' => $token]);

        $response->assertOk();
        $response->assertJsonPath('data.revoked', true);
    }
}
