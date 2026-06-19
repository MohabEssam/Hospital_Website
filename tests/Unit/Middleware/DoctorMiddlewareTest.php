<?php

namespace Tests\Unit\Middleware;

use App\Http\Middleware\DoctorMiddleware;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class DoctorMiddlewareTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_allows_doctor_role(): void
    {
        $user = User::factory()->doctor()->create();

        $request = Request::create('/test');
        $request->setUserResolver(fn () => $user);

        $middleware = new DoctorMiddleware;
        $response = $middleware->handle($request, fn () => new Response('OK'));

        $this->assertSame(200, $response->getStatusCode());
    }

    public function test_rejects_non_doctor_role(): void
    {
        $user = User::factory()->patient()->create();

        $request = Request::create('/test');
        $request->setUserResolver(fn () => $user);

        $this->expectException(HttpException::class);

        $middleware = new DoctorMiddleware;
        $middleware->handle($request, fn () => new Response('OK'));
    }

    public function test_rejects_guest(): void
    {
        $request = Request::create('/test');
        $request->setUserResolver(fn () => null);

        $this->expectException(HttpException::class);

        $middleware = new DoctorMiddleware;
        $middleware->handle($request, fn () => new Response('OK'));
    }

    public function test_rejects_admin_role(): void
    {
        $user = User::factory()->admin()->create();

        $request = Request::create('/test');
        $request->setUserResolver(fn () => $user);

        $this->expectException(HttpException::class);

        $middleware = new DoctorMiddleware;
        $middleware->handle($request, fn () => new Response('OK'));
    }
}
