<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use App\Http\Resources\V1\CategoriaResource;
use App\Http\Resources\V1\CategoriaCollection;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CategoriaController extends Controller
{
    /**
     * Listar todas las categorías
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Categoria::withCount('incidencias');

            // Filtros
            if ($request->has('activas')) {
                $query->where('disabled', false);
            }

            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('nombre_categoria', 'LIKE', "%{$search}%")
                      ->orWhere('descripcion', 'LIKE', "%{$search}%");
                });
            }

            $categorias = $query->get();

            return response()->json([
                'success' => true,
                'message' => 'Categorías obtenidas exitosamente',
                'data' => CategoriaResource::collection($categorias)
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener categorías: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mostrar una categoría específica
     */
    public function show($id): JsonResponse
    {
        try {
            $categoria = Categoria::withCount('incidencias')->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Categoría obtenida exitosamente',
                'data' => new CategoriaResource($categoria)
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Categoría no encontrada',
                'error_code' => 'CATEGORIA_NOT_FOUND'
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener categoría: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener estadísticas de categorías
     */
    public function estadisticas(): JsonResponse
    {
        try {
            $categorias = Categoria::withCount(['incidencias', 'incidencias as resueltas_count' => function($query) {
                $query->where('estado', 'resuelto');
            }])->get();

            $data = $categorias->map(function($categoria) {
                return [
                    'id' => $categoria->id,
                    'nombre' => $categoria->nombre_categoria,
                    'total_incidencias' => $categoria->incidencias_count,
                    'incidencias_resueltas' => $categoria->resueltas_count,
                    'incidencias_pendientes' => $categoria->incidencias_count - $categoria->resueltas_count,
                    'tasa_resolucion' => $categoria->incidencias_count > 0 
                        ? round(($categoria->resueltas_count / $categoria->incidencias_count) * 100, 2) 
                        : 0,
                    'ui_config' => [
                        'color' => $this->getColorForCategory($categoria->nombre_categoria),
                        'icono' => $this->getIconForCategory($categoria->nombre_categoria),
                        'activo' => !$categoria->disabled
                    ]
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Estadísticas de categorías obtenidas exitosamente',
                'data' => $data
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener estadísticas: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getColorForCategory(string $nombre): string
    {
        $colors = [
            'Infraestructura' => '#3B82F6',
            'Alumbrado Público' => '#F59E0B',
            'Limpieza' => '#10B981',
            'Agua Potable' => '#06B6D4',
            'Áreas Verdes' => '#84CC16'
        ];
        
        return $colors[$nombre] ?? '#6B7280';
    }
    
    private function getIconForCategory(string $nombre): string
    {
        $icons = [
            'Infraestructura' => 'fa-hammer',
            'Alumbrado Público' => 'fa-lightbulb',
            'Limpieza' => 'fa-broom',
            'Agua Potable' => 'fa-tint',
            'Áreas Verdes' => 'fa-tree'
        ];
        
        return $icons[$nombre] ?? 'fa-exclamation-triangle';
    }
}
