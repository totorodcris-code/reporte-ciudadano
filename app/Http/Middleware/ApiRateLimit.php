<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class ApiRateLimit
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Rate limiting basado en el tipo de endpoint
        $key = $this->resolveRequestSignature($request);
        
        // Diferentes límites según el endpoint
        $maxAttempts = $this->getMaxAttempts($request);
        $decaySeconds = $this->getDecaySeconds($request);
        
        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            return response()->json([
                'success' => false,
                'message' => 'Too many requests. Please try again later.',
                'error_code' => 'RATE_LIMIT_EXCEEDED',
                'retry_after' => RateLimiter::availableIn($key)
            ], 429);
        }
        
        RateLimiter::hit($key, $decaySeconds);
        
        // Agregar headers de rate limiting
        $response = $next($request);
        $response->headers->set('X-RateLimit-Limit', $maxAttempts);
        $response->headers->set('X-RateLimit-Remaining', $maxAttempts - RateLimiter::attempts($key));
        
        return $response;
    }
    
    /**
     * Resolve the rate limiting signature.
     */
    protected function resolveRequestSignature(Request $request): string
    {
        // Usar IP + user_id si está autenticado, o solo IP si no
        if ($request->user()) {
            return 'api:' . $request->user()->id . ':' . $request->ip() . ':' . $request->route()->getName();
        }
        
        return 'api:guest:' . $request->ip() . ':' . $request->route()->getName();
    }
    
    /**
     * Get max attempts based on endpoint type.
     */
    protected function getMaxAttempts(Request $request): int
    {
        $routeName = $request->route()->getName();
        
        // Login y register - más restrictivos
        if (str_contains($routeName, 'login') || str_contains($routeName, 'register')) {
            return 5; // 5 intentos por minuto
        }
        
        // Endpoints de escritura - moderados
        if (in_array($request->method(), ['POST', 'PUT', 'DELETE'])) {
            return 60; // 60 por minuto
        }
        
        // Endpoints de lectura - más permisivos
        return 120; // 120 por minuto
    }
    
    /**
     * Get decay seconds based on endpoint type.
     */
    protected function getDecaySeconds(Request $request): int
    {
        $routeName = $request->route()->getName();
        
        // Login y register - 1 minuto
        if (str_contains($routeName, 'login') || str_contains($routeName, 'register')) {
            return 60;
        }
        
        // Otros endpoints - 1 minuto
        return 60;
    }
}
