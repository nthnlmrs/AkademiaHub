<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminLabMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || !$request->user()->isAdminLab()) {
            abort(403, 'Unauthorized. Admin Laboratory access required.');
        }

        return $next($request);
    }
}
