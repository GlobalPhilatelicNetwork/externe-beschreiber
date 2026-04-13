<?php

namespace Tests\Feature\Middleware;

use App\Http\Middleware\ConsignmentOpenMiddleware;
use App\Models\Consignment;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ConsignmentOpenMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_open_consignment_passes(): void
    {
        $consignment = Consignment::factory()->create(['status' => 'open']);
        $request = Request::create('/test', 'GET');
        $route = new \Illuminate\Routing\Route('GET', '/test/{consignment}', fn() => 'OK');
        $route->bind($request);
        $request->setRouteResolver(fn() => $route);
        $route->setParameter('consignment', $consignment);

        $middleware = new ConsignmentOpenMiddleware();
        $response = $middleware->handle($request, fn($req) => new Response('OK'));

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_closed_consignment_returns_403(): void
    {
        $consignment = Consignment::factory()->create(['status' => 'closed']);
        $request = Request::create('/test', 'GET');
        $route = new \Illuminate\Routing\Route('GET', '/test/{consignment}', fn() => 'OK');
        $route->bind($request);
        $request->setRouteResolver(fn() => $route);
        $route->setParameter('consignment', $consignment);

        $middleware = new ConsignmentOpenMiddleware();
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $middleware->handle($request, fn($req) => new Response('OK'));
    }
}
