<?php

namespace App\Http\Controllers;

use App\Models\Voto;
use App\Models\Incidencias;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class VotoController extends Controller
{
    /**
     * Obtener votos de una incidencia
     */
    public function index(Request $request, $incidenciaId): JsonResponse
    {
        try {
            $incidencia = Incidencias::findOrFail($incidenciaId);
            
            $votosPositivos = $incidencia->votosPositivos()->count();
            $votosNegativos = $incidencia->votosNegativos()->count();
            $totalVotos = $votosPositivos + $votosNegativos;
            
            // Verificar si el usuario actual ya votó
            $userVoto = null;
            if (Auth::check()) {
                $userVoto = $incidencia->votos()
                    ->where('user_id', Auth::id())
                    ->first();
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Votos obtenidos exitosamente',
                'data' => [
                    'positivos' => $votosPositivos,
                    'negativos' => $votosNegativos,
                    'total' => $totalVotos,
                    'user_voto' => $userVoto ? $userVoto->tipo : null
                ]
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener votos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear o actualizar un voto
     */
    public function store(Request $request): JsonResponse
    {
        try {
            // Log básico para debugging
            Log::info('Voto store method called', [
                'request_data' => $request->all(),
                'method' => $request->method(),
                'url' => $request->fullUrl()
            ]);

            // Validación básica
            $validated = $request->validate([
                'incidencia_id' => 'required|integer|exists:incidencias,id',
                'tipo' => 'required|in:positivo,negativo'
            ]);

            Log::info('Validation passed', ['validated_data' => $validated]);

            // Verificar autenticación y obtener user_id
            $userId = null;
            $user = null;
            
            // Intentar obtener user_id de múltiples formas
            if (Auth::check()) {
                $user = Auth::user();
                $userId = $user->id;
                Log::info('User authenticated via Auth', ['user_id' => $userId, 'user_name' => $user->name]);
            } else {
                // Fallback: intentar obtener desde token o session
                $token = $request->bearerToken();
                if ($token) {
                    try {
                        // Intentar decodificar token JWT manualmente
                        $parts = explode('.', $token);
                        if (isset($parts[1])) {
                            $payload = json_decode(base64_decode($parts[1]), true);
                            if (isset($payload['sub'])) {
                                $userId = $payload['sub'];
                                Log::info('User ID extracted from token', ['user_id' => $userId]);
                            }
                        }
                    } catch (\Exception $e) {
                        Log::warning('Failed to decode token', ['error' => $e->getMessage()]);
                    }
                }
                
                // Último fallback: intentar desde sesión
                if (!$userId && session()->has('user_id')) {
                    $userId = session('user_id');
                    Log::info('User ID from session', ['user_id' => $userId]);
                }
            }
            
            // Validación final de user_id
            if (!$userId) {
                Log::error('No user ID found', [
                    'auth_check' => Auth::check(),
                    'token_exists' => !!$request->bearerToken(),
                    'session_user_id' => session('user_id')
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no autenticado o sesión inválida'
                ], 401);
            }
            
            // Obtener usuario si no tenemos el objeto
            if (!$user) {
                $user = \App\Models\User::find($userId);
                if (!$user) {
                    Log::error('User not found in database', ['user_id' => $userId]);
                    return response()->json([
                        'success' => false,
                        'message' => 'Usuario no encontrado'
                    ], 401);
                }
            }
            
            Log::info('User validation successful', ['user_id' => $userId, 'user_name' => $user->name]);

            // Verificar incidencia
            $incidencia = Incidencias::findOrFail($validated['incidencia_id']);
            Log::info('Incidencia found', ['incidencia_id' => $incidencia->id]);

            // Verificar que no vote en propia incidencia
            if ($incidencia->user_id == $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'No puedes votar en tu propia incidencia'
                ], 400);
            }

            // Buscar voto existente usando el userId validado
            $votoExistente = Voto::where('user_id', $userId)
                ->where('incidencia_id', $validated['incidencia_id'])
                ->first();

            if ($votoExistente) {
                Log::info('Existing vote found', ['vote_id' => $votoExistente->id, 'tipo' => $votoExistente->tipo]);
                
                if ($votoExistente->tipo == $validated['tipo']) {
                    // Eliminar voto
                    $votoExistente->delete();
                    $action = 'removed';
                    Log::info('Vote removed');
                } else {
                    // Actualizar voto
                    $votoExistente->update(['tipo' => $validated['tipo']]);
                    $action = 'updated';
                    Log::info('Vote updated');
                }
            } else {
                // Crear nuevo voto usando el userId validado
                Log::info('Creating new vote', [
                    'user_id' => $userId,
                    'incidencia_id' => $validated['incidencia_id'],
                    'tipo' => $validated['tipo']
                ]);

                $voto = Voto::create([
                    'user_id' => $userId,
                    'incidencia_id' => $validated['incidencia_id'],
                    'tipo' => $validated['tipo']
                ]);
                
                $action = 'created';
                Log::info('New vote created', ['vote_id' => $voto->id, 'user_id' => $userId]);
            }

            // Recalcular estadísticas
            $votosPositivos = $incidencia->votos()->where('tipo', 'positivo')->count();
            $votosNegativos = $incidencia->votos()->where('tipo', 'negativo')->count();
            $totalVotos = $votosPositivos + $votosNegativos;

            Log::info('Stats recalculated', [
                'positivos' => $votosPositivos,
                'negativos' => $votosNegativos,
                'total' => $totalVotos
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Voto procesado exitosamente',
                'data' => [
                    'action' => $action,
                    'estadisticas' => [
                        'positivos' => $votosPositivos,
                        'negativos' => $votosNegativos,
                        'total' => $totalVotos
                    ]
                ]
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error', ['errors' => $e->errors()]);
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('General error in vote store', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar voto: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar un voto
     */
    public function destroy($id): JsonResponse
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no autenticado'
                ], 401);
            }

            $voto = Voto::findOrFail($id);

            // Solo el autor o admin puede eliminar
            if ($voto->user_id != $user->id && $user->role != 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permiso para eliminar este voto'
                ], 403);
            }

            $voto->delete();

            return response()->json([
                'success' => true,
                'message' => 'Voto eliminado exitosamente'
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar voto: ' . $e->getMessage()
            ], 500);
        }
    }
}
