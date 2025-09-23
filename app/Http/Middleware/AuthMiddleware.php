<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthMiddleware
{
    function handle(Request $request, Closure $next): Response
    {
        $authCode = config('auth.auth_bearer_code');

        if (!($bearer = $request->bearerToken()) || $bearer !== $authCode) {
            abort(403);
        }

        return $next($request);
    }
}
