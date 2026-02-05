<?php

namespace Tests\Feature\Import;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ImportEndpointsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_queue_import_teams(): void
    {
        $admin = User::factory()->admin()->create();
        Sanctum::actingAs($admin, ['import:*']);

        $response = $this->postJson('/api/v1/import/teams');

        $response->assertStatus(202);
        $response->assertJsonPath('data.queued', true);
    }

    public function test_non_admin_cannot_queue_import_teams(): void
    {
        $user = User::factory()->user()->create();
        Sanctum::actingAs($user, ['import:*']);

        $response = $this->postJson('/api/v1/import/teams');

        $response->assertForbidden();
    }
}
