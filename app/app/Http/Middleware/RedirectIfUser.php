<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = 'user')
    {
        if (!Auth::guard('user')->check()) {
            return $next($request);
        }

        if (Auth::guard('user')->check() && Auth::guard('user')->user()->isPlateforme()) {
            return redirect()->route('tenant.plateforme.home');
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