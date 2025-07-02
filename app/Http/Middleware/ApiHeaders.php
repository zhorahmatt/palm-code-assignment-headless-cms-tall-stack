<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApiHeaders
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        
        $response->headers->set('X-API-Version', '1.0.0');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        
        return $response;
    }
}
