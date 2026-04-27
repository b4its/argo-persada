<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckAdminRoleRedirect
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Izinkan akses jika route adalah logout atau welcome untuk mencegah loop
        if ($request->routeIs('filament.admin.auth.logout') || $request->routeIs('welcome')) {
            return $next($request);
        }

        // 2. Cek apakah user sudah login
        if (Auth::check()) {
            // 3. Jika login tapi BUKAN admin, lempar ke welcome
            if (Auth::user()->role !== 'admin') {
                return redirect()->route('welcome');
            }
            
            // Jika login dan admin, izinkan lanjut
            return $next($request);
        }

        // 4. Jika belum login sama sekali, biasanya biarkan sistem auth bawaan yang menangani
        // atau redirect ke login
        return redirect()->route('filament.admin.auth.login');
    }
}
