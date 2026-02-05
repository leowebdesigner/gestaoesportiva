<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class XAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_x_token(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/auth/x-token', [
            'name' => 'integration',
        ]);

        $response->assertCreated();
        $response->assertJsonStructure(['data' => ['token', 'expires_at']]);
    }
}
