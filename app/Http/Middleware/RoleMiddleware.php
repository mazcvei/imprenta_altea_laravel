<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'message' => 'No autenticado'
            ], 401);
        }

        if (!$user->role || !in_array($user->role->name, $roles)) {
            return response()->json([
                'message' => 'Sin permisos'
            ], 403);
        }

        return $next($request);
    }
}
