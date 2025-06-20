<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect('login');
        }

        $userRole = Auth::user()->role;

        foreach ($roles as $role) {
            $roleNames = explode('|', $role);
            foreach ($roleNames as $roleName) {
                if ($userRole === $roleName) {
                    return $next($request);
                }
            }
        }

        abort(403, 'No tienes permiso para acceder a esta página.');
    }
}
