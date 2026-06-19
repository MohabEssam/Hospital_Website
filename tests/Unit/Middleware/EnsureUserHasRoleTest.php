<?php

namespace Tests\Unit\Middleware;

use App\Http\Middleware\EnsureUserHasRole;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class EnsureUserHasRoleTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_allows_user_with_matching_single_role(): void
    {
        $user = User::factory()->admin()->create();

        $request = Request::create('/test');
        $request->setUserResolver(fn () => $user);

        $middleware = new EnsureUserHasRole;
        $response = $middleware->handle($request, fn () => new Response('OK'), User::ROLE_ADMIN);

        $this->assertSame(200, $response->getStatusCode());
    }

    public function test_allows_user_with_one_of_multiple_roles(): void
    {
        $user = User::factory()->labStaff()->create();

        $request = Request::create('/test');
        $request->setUserResolver(fn () => $user);

        $middleware = new EnsureUserHasRole;
        $response = $middleware->handle($request, fn () => new Response('OK'), User::ROLE_LAB, User::ROLE_PHARMACY);

        $this->assertSame(200, $response->getStatusCode());
    }

    public function test_rejects_user_without_matching_role(): void
    {
        $user = User::factory()->patient()->create();

        $request = Request::create('/test');
        $request->setUserResolver(fn () => $user);

        $this->expectException(HttpException::class);

        $middleware = new EnsureUserHasRole;
        $middleware->handle($request, fn () => new Response('OK'), User::ROLE_ADMIN, User::ROLE_DOCTOR);
    }

    public function test_rejects_guest(): void
    {
        $request = Request::create('/test');
        $request->setUserResolver(fn () => null);

        $this->expectException(HttpException::class);

        $middleware = new EnsureUserHasRole;
        $middleware->handle($request, fn () => new Response('OK'), User::ROLE_ADMIN);
    }
}
