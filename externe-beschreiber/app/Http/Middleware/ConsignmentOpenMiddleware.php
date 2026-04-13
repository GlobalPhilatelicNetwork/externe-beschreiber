<?php

namespace App\Http\Middleware;

use App\Models\Consignment;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ConsignmentOpenMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $consignment = $request->route('consignment');
        if ($consignment instanceof Consignment && !$consignment->isOpen()) {
            abort(403, 'Consignment is closed');
        }
        return $next($request);
    }
}
