<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        if (!Auth::guard('user')->check()) {
            return redirect()->route('tenant.login');
        }

        if (Auth::guard('user')->check() && Auth::guard('user')->user()->isPlateforme()) {
            return $next($request);
        }

        if (Auth::guard('user')->check() && Auth::guard('user')->user()->isProject()) {
            return redirect()->route('tenant.project.home');
        }

        if (Auth::guard('user')->check() && Auth::guard('user')->user()->isGroup()) {
            return redirect()->route('tenant.group.home');
        }

        abort(403, 'Unauthorized');
    }
}