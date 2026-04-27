<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckLogistikRoleRedirect
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->routeIs('filament.logistik.auth.logout')) {
            return $next($request);
        }

        if (Auth::check()) {
            $role = Auth::user()->role;
            // Izinkan jika role adalah admin ATAU logistik
            if ($role === 'admin' || $role === 'logistik') {
                return $next($request);
            }
        }

        return redirect()->route('welcome');
    }
}
