<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleVendedor
{
    // No usado en Sprint 1 — conservado por compatibilidad con bootstrap/app.php
    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }
}
