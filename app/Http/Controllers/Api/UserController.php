<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of all users.
     */
    public function index(): JsonResponse
    {
        $users = User::withCount('incidencias')->get();
        return response()->json($users, 200);
    }

    /**
     * Change user role.
     */
    public function updateRole(Request $request, User $user): JsonResponse
    {
        $request->merge(['role' => $request->input('role', $request->input('rol'))]);

        $request->validate([
            'role' => 'required|in:admin,usuario'
        ]);
        
        $user->update(['role' => $request->role]);
        return response()->json(['message' => 'Rol actualizado exitosamente'], 200);
    }

    /**
     * Delete a user.
     */
    public function destroy(User $user): JsonResponse
    {
        $user->delete();
        return response()->json(['message' => 'Usuario eliminado exitosamente'], 200);
    }
}
