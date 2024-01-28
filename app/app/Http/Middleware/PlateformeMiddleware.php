<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PlateformeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
         // Use the `user` guard explicitly instead of the global `auth()` helper
        if (auth('user')->check() && auth('user')->user()->isPlateforme()) {
            return $next($request);
        }

        // If not authenticated or not a plateforme user, abort with a 403 error
        abort(403, 'Unauthorized');
    }
}
