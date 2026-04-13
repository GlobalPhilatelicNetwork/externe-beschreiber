<?php

namespace Tests\Feature\Middleware;

use App\Http\Middleware\ApiKeyMiddleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ApiKeyMiddlewareTest extends TestCase
{
    public function test_valid_api_key_passes(): void
    {
        config(['services.api.key' => 'test-api-key']);
        $request = Request::create('/test', 'GET');
        $request->headers->set('X-API-Key', 'test-api-key');

        $middleware = new ApiKeyMiddleware();
        $response = $middleware->handle($request, fn($req) => new Response('OK'));

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_missing_api_key_returns_401(): void
    {
        config(['services.api.key' => 'test-api-key']);
        $request = Request::create('/test', 'GET');

        $middleware = new ApiKeyMiddleware();
        $response = $middleware->handle($request, fn($req) => new Response('OK'));

        $this->assertEquals(401, $response->getStatusCode());
    }

    public function test_wrong_api_key_returns_401(): void
    {
        config(['services.api.key' => 'test-api-key']);
        $request = Request::create('/test', 'GET');
        $request->headers->set('X-API-Key', 'wrong-key');

        $middleware = new ApiKeyMiddleware();
        $response = $middleware->handle($request, fn($req) => new Response('OK'));

        $this->assertEquals(401, $response->getStatusCode());
    }
}
