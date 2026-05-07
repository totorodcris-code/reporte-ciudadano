<?php

namespace App\Http\Controllers;

use App\Http\Requests\ComentarioRequest;
use App\Models\Comentario;
use App\Models\Incidencias;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ComentarioController extends Controller
{
    /**
     * Obtener comentarios de una incidencia
     */
    public function index(Request $request, $incidenciaId): JsonResponse
    {
        try {
            $incidencia = Incidencias::findOrFail($incidenciaId);
            
            $comentarios = $incidencia->comentarios()
                ->with(['user:id,name,email', 'replies.user:id,name,email'])
                ->approved()
                ->root()
                ->orderBy('created_at', 'desc')
                ->paginate(10);
            
            return response()->json([
                'success' => true,
                'message' => 'Comentarios obtenidos exitosamente',
                'data' => $comentarios
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener comentarios: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear un nuevo comentario
     */
    public function store(Request $request): JsonResponse
    {
        try {
            // Temporalmente sin validación para aislar el problema
            // $validated = $request->validate([
            //     'contenido' => 'required|string|max:1000',
            //     'incidencia_id' => 'required|exists:incidencias,id',
            //     'parent_id' => 'nullable|exists:comentarios,id'
            // ]);

            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no autenticado'
                ], 401);
            }

            // Verificar que la incidencia exista
            $incidencia = Incidencias::find($request->incidencia_id);
            if (!$incidencia) {
                return response()->json([
                    'success' => false,
                    'message' => 'La incidencia no existe'
                ], 404);
            }

            // Si es una respuesta, verificar que el comentario padre exista
            if ($request->parent_id) {
                $parentComentario = Comentario::find($request->parent_id);
                if (!$parentComentario) {
                    return response()->json([
                        'success' => false,
                        'message' => 'El comentario padre no existe'
                    ], 404);
                }
                if ($parentComentario->incidencia_id != $request->incidencia_id) {
                    return response()->json([
                        'success' => false,
                        'message' => 'El comentario padre no pertenece a esta incidencia'
                    ], 400);
                }
            }

            $comentario = new Comentario();
            $comentario->descripcion = $request->contenido; // Usar campo descripcion de la tabla real
            $comentario->contenido = $request->contenido; // También guardar en contenido para futuro
            $comentario->user_id = $user->id;
            $comentario->incidencia_id = $request->incidencia_id;
            $comentario->parent_id = $request->parent_id;
            $comentario->is_approved = true; // Auto-aprobar para simplificar
            $comentario->save();

            // Cargar relaciones para la respuesta
            $comentario->load(['user:id,name,email']);

            // Enviar notificación al dueño de la incidencia si no es el mismo usuario
            if ($incidencia->user_id != $user->id) {
                // Aquí se podría implementar notificación Pusher/WebSocket
            }

            return response()->json([
                'success' => true,
                'message' => 'Comentario creado exitosamente',
                'data' => $comentario
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear comentario: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar un comentario
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $request->validate([
                'contenido' => 'required|string|max:1000'
            ]);

            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no autenticado'
                ], 401);
            }

            $comentario = Comentario::findOrFail($id);

            // Solo el autor puede editar su comentario
            if ($comentario->user_id != $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permiso para editar este comentario'
                ], 403);
            }

            $comentario->update([
                'contenido' => $request->contenido
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Comentario actualizado exitosamente',
                'data' => $comentario
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar comentario: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar un comentario
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

            $comentario = Comentario::findOrFail($id);

            // Solo el autor o admin puede eliminar
            if ($comentario->user_id != $user->id && $user->role != 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permiso para eliminar este comentario'
                ], 403);
            }

            $comentario->delete();

            return response()->json([
                'success' => true,
                'message' => 'Comentario eliminado exitosamente'
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar comentario: ' . $e->getMessage()
            ], 500);
        }
    }
}
