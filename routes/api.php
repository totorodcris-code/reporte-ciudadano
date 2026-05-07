<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\IncidenciaController;
use App\Http\Controllers\Api\ComentarioController;
use App\Http\Controllers\Api\VotoController;
use App\Http\Controllers\Api\CategoriaController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\V1\AuthController as AuthControllerV1;
use App\Http\Controllers\Api\V1\CategoriaController as CategoriaControllerV1;

/*
|--------------------------------------------------------------------------
| API Routes Version 1
|--------------------------------------------------------------------------
|
| Rutas para la versión 1 de la API móvil
|
*/

Route::prefix('v1')->group(function () {
    
    // Authentication routes
    Route::post('auth/login', [AuthControllerV1::class, 'login']);
    Route::post('auth/register', [AuthControllerV1::class, 'register']);
    
    Route::middleware('auth:api')->group(function () {
        // Auth endpoints
        Route::get('auth/me', [AuthControllerV1::class, 'me']);
        Route::post('auth/refresh', [AuthControllerV1::class, 'refresh']);
        Route::post('auth/logout', [AuthControllerV1::class, 'logout']);
        Route::put('auth/profile', [AuthControllerV1::class, 'updateProfile']);
        
        // Incidencias endpoints
        Route::get('incidencias', [IncidenciaController::class, 'index']);
        Route::post('incidencias', [IncidenciaController::class, 'store']);
        Route::get('incidencias/{id}', [IncidenciaController::class, 'show']);
        Route::put('incidencias/{id}', [IncidenciaController::class, 'update']);
        Route::delete('incidencias/{id}', [IncidenciaController::class, 'destroy']);
        Route::get('incidencias/mis-reportes', [IncidenciaController::class, 'misIncidencias']);
        Route::get('incidencias/estadisticas', [IncidenciaController::class, 'estadisticas']);
        
        // Categorías endpoints
        Route::get('categorias', [CategoriaControllerV1::class, 'index']);
        Route::get('categorias/{id}', [CategoriaControllerV1::class, 'show']);
        Route::get('categorias/estadisticas', [CategoriaControllerV1::class, 'estadisticas']);
        
        // Comentarios y votos (mantener compatibilidad)
        Route::apiResource('comentarios', ComentarioController::class);
        Route::apiResource('votos', VotoController::class);
        
        // Usuarios (solo admin)
        Route::middleware('role:admin')->group(function () {
            Route::get('usuarios', [UserController::class, 'index']);
            Route::delete('usuarios/{user}', [UserController::class, 'destroy']);
            Route::put('usuarios/{user}/role', [UserController::class, 'updateRole']);
        });
    });
});

/*
|--------------------------------------------------------------------------
| Legacy API Routes (Backward Compatibility)
|--------------------------------------------------------------------------
|
| Mantener rutas existentes para compatibilidad
|
*/

Route::post('login', [AuthController::class,'login']);
Route::post('register', [AuthController::class,'register']);

Route::middleware('auth:api')->group(function () {
    Route::get('me', [AuthController::class,'me']);
    Route::post('refresh', [AuthController::class,'refresh']);
    Route::post('logout', [AuthController::class,'logout']);
    
    // Rutas de incidencias del usuario
    Route::get('mis-incidencias', [IncidenciaController::class, 'misIncidencias']);
    
    // Rutas de analytics y estadísticas
    Route::get('incidencias/mas-votadas', [IncidenciaController::class, 'masVotadas']);
    Route::get('analytics', [IncidenciaController::class, 'analytics']);
});

Route::apiResource('incidencias', IncidenciaController::class);
// Route::apiResource('comentarios', ComentarioController::class); // Comentado para evitar conflictos de validación
Route::post('comentarios', [ComentarioController::class, 'store']); // Ruta específica para crear comentarios

// Rutas de votación con autenticación requerida
Route::middleware('auth:api')->group(function () {
    Route::get('votos/estadisticas', [VotoController::class, 'estadisticas']);
    Route::get('incidencias/{incidenciaId}/votos', [VotoController::class, 'index']);
    Route::apiResource('votos', VotoController::class);
});

// Rutas específicas para comentarios
Route::get('incidencias/{incidenciaId}/comentarios', [ComentarioController::class, 'index']);
Route::apiResource('categorias', CategoriaController::class);
Route::apiResource('usuarios', UserController::class, ['only' => ['index', 'destroy']]);
Route::put('usuarios/{user}/role', [UserController::class, 'updateRole']);

Route::get('estadisticas', function () {
    $totalReportes = \App\Models\Incidencias::count();
    $resueltos = \App\Models\Incidencias::where('estado', 'resuelto')->count();
    $usuarios = \App\Models\User::count();
    $categorias = \App\Models\Categoria::count();
    
    return response()->json([
        'reportes' => $totalReportes,
        'resueltos' => $resueltos,
        'pendientes' => $totalReportes - $resueltos,
        'usuarios' => $usuarios,
        'categorias' => $categorias
    ]);
});

?>
