<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Voto;
use App\Models\Incidencias;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class VotoController extends Controller
{
    public function index($incidenciaId = null): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'No autorizado'
            ], 401);
        }

        if ($incidenciaId) {
            $votos = Voto::with(['user'])
                        ->where('incidencia_id', $incidenciaId)
                        ->get();

            $userVote = $votos->where('user_id', $user->id)->first();

            return response()->json([
                'success' => true,
                'data' => [
                    'votos' => $votos,
                    'positivos' => $votos->where('tipo', 'positivo')->count(),
                    'negativos' => $votos->where('tipo', 'negativo')->count(),
                    'total' => $votos->count(),
                    'user_voto' => $userVote ? $userVote->tipo : null
                ]
            ], 200);
        }

        if ($user->role === 'admin') {
            $votos = Voto::with(['user', 'incidencia'])
                        ->latest()
                        ->paginate(50);
        } else {
            $votos = Voto::with(['user', 'incidencia'])
                        ->whereHas('incidencia', function ($query) use ($user) {
                            $query->where('user_id', $user->id);
                        })
                        ->latest()
                        ->paginate(50);
        }

        return response()->json([
            'success' => true,
            'data' => $votos->items(),
            'pagination' => [
                'current_page' => $votos->currentPage(),
                'last_page' => $votos->lastPage(),
                'per_page' => $votos->perPage(),
                'total' => $votos->total(),
                'from' => $votos->firstItem(),
                'to' => $votos->lastItem()
            ]
        ], 200);
    }

    public function estadisticas(): JsonResponse
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'No autorizado'
            ], 401);
        }

        if ($user->role === 'admin') {
            $totalVotos = Voto::count();
            $votosPositivos = Voto::where('tipo', 'positivo')->count();
            $votosNegativos = Voto::where('tipo', 'negativo')->count();
            $incidenciasConVotos = Voto::distinct()->count('incidencia_id');
            
            $votosPorDia = Voto::selectRaw('DATE(created_at) as fecha, COUNT(*) as total')
                              ->where('created_at', '>=', now()->subDays(7))
                              ->groupByRaw('DATE(created_at)')
                              ->orderBy('fecha')
                              ->get();
        } else {
            $totalVotos = Voto::whereHas('incidencia', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->count();
            
            $votosPositivos = Voto::whereHas('incidencia', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->where('tipo', 'positivo')->count();
            
            $votosNegativos = Voto::whereHas('incidencia', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->where('tipo', 'negativo')->count();
            
            $incidenciasConVotos = Voto::whereHas('incidencia', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->distinct()->count('incidencia_id');
            
            $votosPorDia = Voto::selectRaw('DATE(created_at) as fecha, COUNT(*) as total')
                              ->whereHas('incidencia', function ($query) use ($user) {
                                  $query->where('user_id', $user->id);
                              })
                              ->where('created_at', '>=', now()->subDays(7))
                              ->groupByRaw('DATE(created_at)')
                              ->orderBy('fecha')
                              ->get();
        }

        return response()->json([
            'success' => true,
            'data' => [
                'total_votos' => $totalVotos,
                'votos_positivos' => $votosPositivos,
                'votos_negativos' => $votosNegativos,
                'incidencias_con_votos' => $incidenciasConVotos,
                'porcentaje_positivos' => $totalVotos > 0 ? round(($votosPositivos / $totalVotos) * 100, 2) : 0,
                'porcentaje_negativos' => $totalVotos > 0 ? round(($votosNegativos / $totalVotos) * 100, 2) : 0,
                'votos_por_dia' => $votosPorDia
            ]
        ], 200);
    }

    public function store(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Debes iniciar sesión para votar'
                ], 401);
            }

            $validate = $request->validate([
                'tipo' => 'required|in:positivo,negativo',
                'incidencia_id' => 'required|exists:incidencias,id',
            ]);

            $incidencia = Incidencias::find($request->incidencia_id);
            if (!$incidencia) {
                return response()->json([
                    'success' => false,
                    'message' => 'La incidencia no existe'
                ], 404);
            }

            if ($incidencia->user_id === $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'No puedes votar en tu propia incidencia'
                ], 403);
            }

            $existingVote = Voto::where('user_id', $user->id)
                               ->where('incidencia_id', $request->incidencia_id)
                               ->first();

            if ($existingVote) {
                if ($existingVote->tipo === $request->tipo) {
                    $existingVote->delete();

                    return response()->json([
                        'success' => true,
                        'message' => 'Voto eliminado',
                        'data' => [
                            'estadisticas' => [
                                'positivos' => Voto::where('incidencia_id', $request->incidencia_id)->where('tipo', 'positivo')->count(),
                                'negativos' => Voto::where('incidencia_id', $request->incidencia_id)->where('tipo', 'negativo')->count(),
                                'total' => Voto::where('incidencia_id', $request->incidencia_id)->count()
                            ]
                        ]
                    ], 200);
                } else {
                    $existingVote->tipo = $request->tipo;
                    $existingVote->save();

                    return response()->json([
                        'success' => true,
                        'message' => 'Voto actualizado',
                        'data' => [
                            'estadisticas' => [
                                'positivos' => Voto::where('incidencia_id', $request->incidencia_id)->where('tipo', 'positivo')->count(),
                                'negativos' => Voto::where('incidencia_id', $request->incidencia_id)->where('tipo', 'negativo')->count(),
                                'total' => Voto::where('incidencia_id', $request->incidencia_id)->count()
                            ]
                        ]
                    ], 200);
                }
            }

            $voto = Voto::create([
                'tipo' => $request->tipo,
                'incidencia_id' => $request->incidencia_id,
                'user_id' => $user->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Voto registrado',
                'data' => [
                    'estadisticas' => [
                        'positivos' => Voto::where('incidencia_id', $request->incidencia_id)->where('tipo', 'positivo')->count(),
                        'negativos' => Voto::where('incidencia_id', $request->incidencia_id)->where('tipo', 'negativo')->count(),
                        'total' => Voto::where('incidencia_id', $request->incidencia_id)->count()
                    ]
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar voto: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(Voto $voto): JsonResponse
    {
        return response()->json($voto->load(['user', 'incidencia']));
    }

    public function destroy(Voto $voto): JsonResponse
    {
        $voto->delete();
        return response()->json(['message' => 'Voto eliminado con exito'], 200);
    }

}
