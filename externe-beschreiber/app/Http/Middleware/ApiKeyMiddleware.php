<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiKeyMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = config('services.api.key');
        if (!$apiKey || $request->header('X-API-Key') !== $apiKey) {
            return response()->json(['message' => 'Invalid API key'], 401);
        }
        return $next($request);
    }
}
