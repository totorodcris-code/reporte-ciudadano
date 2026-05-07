<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    /**
     * Login de usuario y generación de token JWT
     */
    public function login(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|exists:users,email',
                'password' => 'required|string|min:6',
                'device_token' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            $credentials = $request->only('email', 'password');

            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Credenciales incorrectas',
                    'error_code' => 'INVALID_CREDENTIALS'
                ], 401);
            }

            $user = JWTAuth::user();

            // Actualizar device token si se proporciona
            if ($request->device_token) {
                $user->device_token = $request->device_token;
                $user->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'Login exitoso',
                'data' => [
                    'access_token' => $token,
                    'token_type' => 'bearer',
                    'expires_in' => config('jwt.ttl') * 60,
                    'user' => [
                        'id' => $user->id,
                        'nombre' => $user->name,
                        'email' => $user->email,
                        'rol' => $user->role,
                        'created_at' => $user->created_at,
                        'perfil' => [
                            'iniciales' => strtoupper(substr($user->name, 0, 2)),
                            'nombre_corto' => explode(' ', $user->name)[0],
                            'es_admin' => $user->role === 'admin'
                        ]
                    ]
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error en el login: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Registro de nuevos usuarios
     */
    public function register(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email',
                'password' => 'required|string|min:8|confirmed',
                'device_token' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'usuario',
                'device_token' => $request->device_token
            ]);

            $token = JWTAuth::fromUser($user);

            return response()->json([
                'success' => true,
                'message' => 'Usuario registrado exitosamente',
                'data' => [
                    'access_token' => $token,
                    'token_type' => 'bearer',
                    'expires_in' => config('jwt.ttl') * 60,
                    'user' => [
                        'id' => $user->id,
                        'nombre' => $user->name,
                        'email' => $user->email,
                        'rol' => $user->role,
                        'created_at' => $user->created_at,
                        'perfil' => [
                            'iniciales' => strtoupper(substr($user->name, 0, 2)),
                            'nombre_corto' => explode(' ', $user->name)[0],
                            'es_admin' => $user->role === 'admin'
                        ]
                    ]
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error en el registro: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener perfil del usuario autenticado
     */
    public function me(): JsonResponse
    {
        try {
            $user = JWTAuth::user();

            return response()->json([
                'success' => true,
                'message' => 'Perfil obtenido exitosamente',
                'data' => [
                    'id' => $user->id,
                    'nombre' => $user->name,
                    'email' => $user->email,
                    'rol' => $user->role,
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at,
                    'perfil' => [
                        'iniciales' => strtoupper(substr($user->name, 0, 2)),
                        'nombre_corto' => explode(' ', $user->name)[0],
                        'es_admin' => $user->role === 'admin'
                    ],
                    'estadisticas' => [
                        'total_reportes' => $user->incidencias()->count(),
                        'reportes_resueltos' => $user->incidencias()->where('estado', 'resuelto')->count(),
                        'reportes_pendientes' => $user->incidencias()->where('estado', 'pendiente')->count()
                    ]
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el perfil: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Logout del usuario
     */
    public function logout(): JsonResponse
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());

            return response()->json([
                'success' => true,
                'message' => 'Sesión cerrada exitosamente'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cerrar sesión: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Refresh token JWT
     */
    public function refresh(): JsonResponse
    {
        try {
            $token = JWTAuth::refresh();
            $user = JWTAuth::user();

            return response()->json([
                'success' => true,
                'message' => 'Token refrescado exitosamente',
                'data' => [
                    'access_token' => $token,
                    'token_type' => 'bearer',
                    'expires_in' => config('jwt.ttl') * 60,
                    'user' => [
                        'id' => $user->id,
                        'nombre' => $user->name,
                        'email' => $user->email,
                        'rol' => $user->role
                    ]
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al refrescar token: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar perfil del usuario
     */
    public function updateProfile(Request $request): JsonResponse
    {
        try {
            $user = JWTAuth::user();

            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required|string|max:255',
                'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $user->id,
                'password' => 'sometimes|required|string|min:8|confirmed',
                'device_token' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            $updateData = $request->only(['name', 'email', 'device_token']);

            if ($request->has('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            $user->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Perfil actualizado exitosamente',
                'data' => [
                    'id' => $user->id,
                    'nombre' => $user->name,
                    'email' => $user->email,
                    'rol' => $user->role,
                    'updated_at' => $user->updated_at
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar perfil: ' . $e->getMessage()
            ], 500);
        }
    }
}
