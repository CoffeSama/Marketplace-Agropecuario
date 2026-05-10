<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleCliente
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login')
                ->with('error', 'Debes iniciar sesión para acceder a esta página.');
        }

        if (auth()->user()->isAdmin()) {
            return redirect()->route('home')
                ->with('info', 'Esta sección es para usuarios no administradores.');
        }

        return $next($request);
    }
}
