<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class CategoriaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $categorias = Categoria::withCount('incidencias')->latest()->get();
        return response()->json($categorias, 200);
    }

    //crear o registrar nuevas categorias
    public function store(Request $request)
    {
        $validate = $request->validate([
            'nombre_categoria' => 'required|string|max:255|unique:categorias,nombre_categoria',
            'descripcion' => 'nullable|string',
        ]);

        $categoria = Categoria::create($validate);
        return response()->json($categoria, 201);
    }

    //mostrar un elemento especifico de categoria 
    public function show(Categoria $categoria): JsonResponse
    {
        return response()->json($categoria->loadCount('incidencias'));
    }

    public function update(Request $request, Categoria $categoria): JsonResponse
    {
        $validate = $request->validate([
            'nombre_categoria' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('categorias', 'nombre_categoria')->ignore($categoria->getKey()),
            ],
            'descripcion' => 'nullable|string',
        ]);

        $categoria->update($validate);
        return response()->json($categoria);
    }

    public function destroy(Categoria $categoria): JsonResponse
    {
        // Verificar si hay incidencias asociadas a esta categoría
        if ($categoria->incidencias()->count() > 0) {
            return response()->json(['message' => 'No se puede eliminar la categoría porque tiene incidencias asociadas'], 409);
        }

        $categoria->delete();
        return response()->json(['message' => 'Categoría eliminada con éxito'], 200);
    }
}
