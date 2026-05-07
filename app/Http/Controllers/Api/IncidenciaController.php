<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Incidencias;
use App\Http\Resources\V1\IncidenciaResource;
use App\Http\Resources\V1\IncidenciaCollection;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class IncidenciaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Incidencias::with(['user', 'categoria'])->latest();
        
        // Filtros para mobile app
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        
        if ($request->has('estado')) {
            $query->where('estado', $request->estado);
        }
        
        if ($request->has('categoria_id')) {
            $query->where('categoria_id', $request->categoria_id);
        }
        
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('titulo', 'LIKE', "%{$search}%")
                  ->orWhere('descripcion', 'LIKE', "%{$search}%")
                  ->orWhere('direccion', 'LIKE', "%{$search}%");
            });
        }
        
        $incidencias = $query->paginate($request->get('per_page', 15));
        
        return response()->json([
            'success' => true,
            'message' => 'Incidencias obtenidas exitosamente',
            'data' => new IncidenciaCollection($incidencias)
        ], 200);
    }
    //crear o registrar nuevas incidencias
    public function store(Request $request): JsonResponse
    {
        try {
            $validate = $request->validate([
                'titulo' => 'required|max:200',
                'descripcion' => 'required|string',
                'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'latitud' => 'required|numeric',
                'longitud' => 'required|numeric',
                'direccion' => 'required|string',
                'categoria_id' => 'required|exists:categorias,id',
                'user_id' => 'required|exists:users,id',
                'prioridad' => 'nullable|integer|min:1|max:5'
            ]);
            
            // Manejar subida de imagen si existe
            if ($request->hasFile('imagen')) {
                $path = $request->file('imagen')->store('incidencias', 'public');
                $validate['imagen'] = $path;
            }
            
            // Valores por defecto
            $validate['estado'] = 'pendiente';
            $validate['prioridad'] = $validate['prioridad'] ?? 1;
            $validate['fecha_reporte'] = now();
            
            $incidencia = Incidencias::create($validate);
            
            return response()->json([
                'success' => true,
                'message' => 'Incidencia creada exitosamente',
                'data' => new IncidenciaResource($incidencia->load(['user', 'categoria']))
            ], 201);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la incidencia: ' . $e->getMessage()
            ], 500);
        }
    }
    //mostrar un elemento especifico de incidencia
    public function show($id): JsonResponse
    {
        try {
            $incidencia = Incidencias::with(['user', 'categoria'])->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'message' => 'Incidencia obtenida exitosamente',
                'data' => new IncidenciaResource($incidencia)
            ], 200);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Incidencia no encontrada',
                'error_code' => 'INCIDENCIA_NOT_FOUND'
            ], 404);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener la incidencia: ' . $e->getMessage()
            ], 500);
        }
    }
    public function update(Request $request, Incidencias $incidencia): JsonResponse
    {
        try {
            $validate = $request->validate([
                'titulo' => 'sometimes|required|max:200',
                'descripcion' => 'sometimes|required|string',
                'imagen' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'latitud' => 'sometimes|required|numeric',
                'longitud' => 'sometimes|required|numeric',
                'direccion' => 'sometimes|required|string',
                'categoria_id' => 'sometimes|required|exists:categorias,id',
                'estado' => 'sometimes|required|in:pendiente,en-progreso,resuelto',
                'prioridad' => 'sometimes|integer|min:1|max:5'
            ]);
            
            // Manejar actualización de imagen si existe
            if ($request->hasFile('imagen')) {
                // Eliminar imagen anterior si existe
                if ($incidencia->imagen) {
                    Storage::disk('public')->delete($incidencia->imagen);
                }
                
                $path = $request->file('imagen')->store('incidencias', 'public');
                $validate['imagen'] = $path;
            }
            
            $incidencia->update($validate);
            
            return response()->json([
                'success' => true,
                'message' => 'Incidencia actualizada exitosamente',
                'data' => new IncidenciaResource($incidencia->load(['user', 'categoria']))
            ], 200);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la incidencia: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function destroy(Incidencias $incidencia): JsonResponse
    {
        try {
            // Eliminar imagen si existe
            if ($incidencia->imagen) {
                Storage::disk('public')->delete($incidencia->imagen);
            }
            
            $incidencia->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Incidencia eliminada exitosamente'
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la incidencia: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Obtener incidencias del usuario autenticado (Formato Legacy para Web)
     */
    public function misIncidencias(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json(['error' => 'Usuario no autenticado'], 401);
            }
            
            $query = Incidencias::with(['user', 'categoria'])
                ->where('user_id', $user->id)
                ->latest();
            
            $incidencias = $query->paginate(15);
            
            // Devolver en formato legacy para compatibilidad con frontend web
            return response()->json($incidencias, 200);
            
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener las incidencias: ' . $e->getMessage()], 500);
        }
    }
    
    /**
     * Obtener estadísticas de incidencias para mobile
     */
    public function estadisticas(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $query = Incidencias::query();
            
            // Si es usuario normal, solo mostrar sus estadísticas
            if ($user && $user->role !== 'admin') {
                $query->where('user_id', $user->id);
            }
            
            $total = $query->count();
            $pendientes = $query->where('estado', 'pendiente')->count();
            $enProgreso = $query->where('estado', 'en-progreso')->count();
            $resueltos = $query->where('estado', 'resuelto')->count();
            
            // Estadísticas por categoría
            $porCategoria = Incidencias::join('categorias', 'incidencias.categoria_id', '=', 'categorias.id')
                ->selectRaw('categorias.nombre_categoria as categoria, COUNT(*) as total')
                ->when($user && $user->role !== 'admin', function($q) use ($user) {
                    return $q->where('incidencias.user_id', $user->id);
                })
                ->groupBy('categorias.id', 'categorias.nombre_categoria')
                ->get();
            
            return response()->json([
                'success' => true,
                'message' => 'Estadísticas obtenidas exitosamente',
                'data' => [
                    'totales' => [
                        'total' => $total,
                        'pendientes' => $pendientes,
                        'en_progreso' => $enProgreso,
                        'resueltos' => $resueltos
                    ],
                    'por_categoria' => $porCategoria,
                    'porcentajes' => [
                        'resueltos' => $total > 0 ? round(($resueltos / $total) * 100, 2) : 0,
                        'pendientes' => $total > 0 ? round(($pendientes / $total) * 100, 2) : 0,
                        'en_progreso' => $total > 0 ? round(($enProgreso / $total) * 100, 2) : 0
                    ]
                ]
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener estadísticas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener estadísticas avanzadas para dashboard de analytics
     */
    /**
     * Obtener las 3 incidencias más votadas
     */
    public function masVotadas(): JsonResponse
    {
        try {
            $incidencias = Incidencias::withCount('votos')
                ->with(['user', 'categoria'])
                ->orderBy('votos_count', 'desc')
                ->limit(3)
                ->get()
                ->map(function ($incidencia) {
                    $positivos = $incidencia->votos()->where('tipo', 'positivo')->count();
                    $total = $incidencia->votos_count;
                    return [
                        'id' => $incidencia->id,
                        'titulo' => $incidencia->titulo,
                        'descripcion' => $incidencia->descripcion,
                        'estado' => $incidencia->estado,
                        'usuario' => $incidencia->user?->name ?? 'Desconocido',
                        'categoria' => $incidencia->categoria?->nombre_categoria ?? 'Sin categoría',
                        'votos_positivos' => $positivos,
                        'votos_totales' => $total,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $incidencias
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener incidencias más votadas: ' . $e->getMessage()
            ], 500);
        }
    }

    public function analytics(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $query = Incidencias::query();
            
            // Si es usuario normal, solo mostrar sus estadísticas
            if ($user && $user->role !== 'admin') {
                $query->where('user_id', $user->id);
            }
            
            // Filtros de fecha
            $startDate = $request->get('start_date');
            $endDate = $request->get('end_date');
            $categoriaId = $request->get('categoria_id');
            
            if ($startDate) {
                $query->whereDate('created_at', '>=', $startDate);
            }
            if ($endDate) {
                $query->whereDate('created_at', '<=', $endDate);
            }
            if ($categoriaId) {
                $query->where('categoria_id', $categoriaId);
            }
            
            // Estadísticas generales
            $total = $query->count();
            $pendientes = (clone $query)->where('estado', 'pendiente')->count();
            $enProgreso = (clone $query)->where('estado', 'en-progreso')->count();
            $resueltos = (clone $query)->where('estado', 'resuelto')->count();
            
            // Estadísticas por categoría
            $porCategoria = Incidencias::join('categorias', 'incidencias.categoria_id', '=', 'categorias.id')
                ->selectRaw('categorias.nombre_categoria as categoria, COUNT(*) as total, 
                    SUM(CASE WHEN incidencias.estado = "resuelto" THEN 1 ELSE 0 END) as resueltos')
                ->when($user && $user->role !== 'admin', function($q) use ($user) {
                    return $q->where('incidencias.user_id', $user->id);
                })
                ->when($categoriaId, function($q) use ($categoriaId) {
                    return $q->where('incidencias.categoria_id', $categoriaId);
                })
                ->groupBy('categorias.id', 'categorias.nombre_categoria')
                ->get();
            
            // Estadísticas por mes (últimos 12 meses)
            $porMes = Incidencias::selectRaw('
                    DATE_FORMAT(created_at, "%Y-%m") as mes,
                    COUNT(*) as total,
                    SUM(CASE WHEN estado = "resuelto" THEN 1 ELSE 0 END) as resueltos
                ')
                ->when($user && $user->role !== 'admin', function($q) use ($user) {
                    return $q->where('user_id', $user->id);
                })
                ->when($startDate, function($q) use ($startDate) {
                    return $q->whereDate('created_at', '>=', $startDate);
                })
                ->when($endDate, function($q) use ($endDate) {
                    return $q->whereDate('created_at', '<=', $endDate);
                })
                ->groupBy('mes')
                ->orderBy('mes', 'desc')
                ->limit(12)
                ->get();
            
            // Estadísticas por día (últimos 30 días)
            $porDia = Incidencias::selectRaw('
                    DATE(created_at) as dia,
                    COUNT(*) as total,
                    SUM(CASE WHEN estado = "resuelto" THEN 1 ELSE 0 END) as resueltos
                ')
                ->when($user && $user->role !== 'admin', function($q) use ($user) {
                    return $q->where('user_id', $user->id);
                })
                ->when($startDate, function($q) use ($startDate) {
                    return $q->whereDate('created_at', '>=', $startDate);
                })
                ->when($endDate, function($q) use ($endDate) {
                    return $q->whereDate('created_at', '<=', $endDate);
                })
                ->whereDate('created_at', '>=', now()->subDays(30))
                ->groupBy('dia')
                ->orderBy('dia', 'desc')
                ->get();
            
            // Tiempo promedio de resolución
            $tiempoPromedioResolucion = Incidencias::selectRaw('
                    AVG(DATEDIFF(resolved_at, created_at)) as promedio_dias
                ')
                ->whereNotNull('resolved_at')
                ->when($user && $user->role !== 'admin', function($q) use ($user) {
                    return $q->where('user_id', $user->id);
                })
                ->when($startDate, function($q) use ($startDate) {
                    return $q->whereDate('created_at', '>=', $startDate);
                })
                ->when($endDate, function($q) use ($endDate) {
                    return $q->whereDate('created_at', '<=', $endDate);
                })
                ->first();
            
            // Usuarios más activos
            $usuariosMasActivos = Incidencias::selectRaw('
                    user_id,
                    COUNT(*) as total_reportes
                ')
                ->when($user && $user->role !== 'admin', function($q) use ($user) {
                    return $q->where('user_id', $user->id);
                })
                ->groupBy('user_id')
                ->orderBy('total_reportes', 'desc')
                ->limit(10)
                ->with('user:id,name,email')
                ->get();
            
            return response()->json([
                'success' => true,
                'message' => 'Estadísticas avanzadas obtenidas exitosamente',
                'data' => [
                    'totales' => [
                        'total' => $total,
                        'pendientes' => $pendientes,
                        'en_progreso' => $enProgreso,
                        'resueltos' => $resueltos,
                        'tasa_resolucion' => $total > 0 ? round(($resueltos / $total) * 100, 2) : 0
                    ],
                    'por_categoria' => $porCategoria,
                    'por_mes' => $porMes,
                    'por_dia' => $porDia,
                    'tiempo_promedio_resolucion' => $tiempoPromedioResolucion ? round($tiempoPromedioResolucion->promedio_dias, 1) : 0,
                    'usuarios_mas_activos' => $usuariosMasActivos->map(function($item) {
                        return [
                            'user_id' => $item->user_id,
                            'nombre' => $item->user->name,
                            'email' => $item->user->email,
                            'total_reportes' => $item->total_reportes
                        ];
                    }),
                    'filtros_aplicados' => [
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                        'categoria_id' => $categoriaId
                    ]
                ]
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener estadísticas avanzadas: ' . $e->getMessage()
            ], 500);
        }
    }
}
?>