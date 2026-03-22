<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return response()->json(['message' => 'No autenticado'], 401);
        }
        $user = auth()->user();
        if (!$user->rol_id || (int) $user->rol_id !== 1) {
            return response()->json(['message' => 'Acceso denegado. Solo administradores.'], 403);
        }
        return $next($request);
    }
}
