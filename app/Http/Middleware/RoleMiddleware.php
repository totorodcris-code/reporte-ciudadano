<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no autenticado',
                'error_code' => 'USER_NOT_AUTHENTICATED'
            ], 401);
        }
        
        if ($user->role !== $role) {
            return response()->json([
                'success' => false,
                'message' => 'Acceso denegado. Se requiere rol de ' . $role,
                'error_code' => 'INSUFFICIENT_PERMISSIONS',
                'required_role' => $role,
                'current_role' => $user->role
            ], 403);
        }
        
        return $next($request);
    }
}
