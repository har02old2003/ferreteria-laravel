<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleRedirect
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();
            
            // Si el usuario tiene rol de admin
            if ($user->role === 'admin') {
                if (!$request->routeIs('admin.*') && !$request->routeIs('dashboard') && !$request->routeIs('profile.*')) {
                    return redirect()->route('dashboard');
                }
            }
            // Si el usuario tiene rol de empleado
            elseif ($user->role === 'employee') {
                if (!$request->routeIs('employee.*') && !$request->routeIs('dashboard') && !$request->routeIs('profile.*')) {
                    return redirect()->route('dashboard');
                }
            }
            // Si no tiene rol específico, redirigir al dashboard general
            else {
                if (!$request->routeIs('dashboard')) {
                    return redirect()->route('dashboard');
                }
            }
        }

        return $next($request);
    }
}
