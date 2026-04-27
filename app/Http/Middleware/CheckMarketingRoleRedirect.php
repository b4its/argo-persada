<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckMarketingRoleRedirect
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->routeIs('filament.marketing.auth.logout')) {
            return $next($request);
        }

        if (Auth::check()) {
            $role = Auth::user()->role;
            // Izinkan jika role adalah admin ATAU marketing
            if ($role === 'admin' || $role === 'marketing') {
                return $next($request);
            }
        }

        return redirect()->route('welcome');
    }
}
