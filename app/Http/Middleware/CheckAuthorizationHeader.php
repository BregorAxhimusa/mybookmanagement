<?php

namespace App\Http\Middleware;

use Closure;

class CheckAuthorizationHeader
{
    public function handle($request, Closure $next)
    {
        // Check if the Authorization header is missing or doesn't start with 'Bearer'
        if (!$request->header('Authorization') || !starts_with($request->header('Authorization'), 'Bearer')) {
            return response()->json(['error' => 'Authorization header missing or invalid'], 401);
        }

        // If the Authorization header is present and valid, continue with the request
        return $next($request);
    }
}
