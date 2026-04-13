<?php

namespace Tests\Feature\Middleware;

use App\Http\Middleware\RoleMiddleware;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RoleMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_passes_admin_role_check(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $request = Request::create('/test', 'GET');
        $request->setUserResolver(fn() => $admin);

        $middleware = new RoleMiddleware();
        $response = $middleware->handle($request, fn($req) => new Response('OK'), 'admin');

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_user_fails_admin_role_check(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $request = Request::create('/test', 'GET');
        $request->setUserResolver(fn() => $user);

        $middleware = new RoleMiddleware();
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $middleware->handle($request, fn($req) => new Response('OK'), 'admin');
    }

    public function test_guest_fails_role_check(): void
    {
        $request = Request::create('/test', 'GET');
        $request->setUserResolver(fn() => null);

        $middleware = new RoleMiddleware();
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $middleware->handle($request, fn($req) => new Response('OK'), 'admin');
    }
}
