<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comentario;
use App\Models\Incidencias;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ComentarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $comentarios = Comentario::with(['user', 'incidencia'])->latest()->get();
        return response()->json($comentarios, 200);
    }

    //crear o registrar nuevos comentarios
    public function store(Request $request)
    {
        try {
            // Requerir autenticación
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Debes iniciar sesión para comentar'
                ], 401);
            }

            // Validar los datos
            $validate = $request->validate([
                'descripcion' => 'required|string|max:1000',
                'incidencia_id' => 'required|exists:incidencias,id',
            ]);

            // Verificar que la incidencia exista
            $incidencia = Incidencias::find($request->incidencia_id);
            if (!$incidencia) {
                return response()->json([
                    'success' => false,
                    'message' => 'La incidencia no existe'
                ], 404);
            }

            // Crear el comentario
            $comentario = Comentario::create([
                'descripcion' => $request->descripcion,
                'user_id' => $user->id,
                'incidencia_id' => $request->incidencia_id,
            ]);

            // Cargar relaciones para la respuesta
            $comentario->load(['user:id,name,email']);

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

    //mostrar un elemento especifico de comentario 
    public function show(Comentario $comentario): JsonResponse
    {
        return response()->json($comentario->load(['user', 'incidencia']));
    }

    public function update(Request $request, Comentario $comentario): JsonResponse
    {
        $comentario->update($request->all());
        return response()->json($comentario);
    }

    public function destroy(Comentario $comentario): JsonResponse
    {
        $comentario->delete();
        return response()->json(['message' => 'Comentario eliminado con exito'], 200);
    }
}