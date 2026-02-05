<?php

namespace Tests\Unit\Middleware;

use App\Http\Middleware\EnsureUserHasRole;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class EnsureUserHasRoleTest extends TestCase
{
    public function test_allows_when_user_role_enum_matches_required_role(): void
    {
        $middleware = new EnsureUserHasRole();
        $user = User::factory()->admin()->make();
        $request = Request::create('/api/v1/dummy', 'GET');
        $request->setUserResolver(fn () => $user);

        $response = $middleware->handle($request, fn () => response()->noContent(), 'admin');

        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame(204, $response->getStatusCode());
    }

    public function test_denies_when_user_role_does_not_match_required_role(): void
    {
        $middleware = new EnsureUserHasRole();
        $user = User::factory()->user()->make();
        $request = Request::create('/api/v1/dummy', 'GET');
        $request->setUserResolver(fn () => $user);

        $response = $middleware->handle($request, fn () => response()->noContent(), 'admin');

        $this->assertSame(403, $response->getStatusCode());
    }
}
