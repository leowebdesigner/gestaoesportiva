<?php

namespace Tests\Unit\Repositories;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_find_by_email(): void
    {
        $user = User::factory()->create(['email' => 'repo@example.com']);
        $repo = new UserRepository(new User());

        $found = $repo->findByEmail('repo@example.com');

        $this->assertNotNull($found);
        $this->assertEquals($user->id, $found->id);
    }
}
