<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $userRoleId = (string) auth()->user()->role_id;

        if (!in_array($userRoleId, $roles, true)) {
            abort(403, 'Acceso denegado: tu rol no tiene permisos para ingresar a este panel.');
        }

        return $next($request);
    }
}
