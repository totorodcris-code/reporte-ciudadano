<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{

    // LOGIN
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6'
        ]);

        $credentials = $request->only('email', 'password');

        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json([
                'message' => 'Credenciales incorrectas'
            ], 401);
        }

        return $this->respondWithToken($token);
    }


    // REGISTER
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'usuario',
        ]);

        $token = JWTAuth::fromUser($user);

        return $this->respondWithToken($token);
    }


    // USUARIO AUTENTICADO
    public function me()
    {
        return response()->json(JWTAuth::user());
    }


    // LOGOUT
    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());

        return response()->json([
            'message' => 'Sesión cerrada correctamente'
        ]);
    }


    // REFRESH TOKEN
    public function refresh()
    {
        return $this->respondWithToken(JWTAuth::refresh());
    }


    // RESPUESTA DEL TOKEN
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => config('jwt.ttl') * 60,
            'user' => JWTAuth::user()
        ]);
    }


    // REDIRIGIR A GOOGLE
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }


    // CALLBACK GOOGLE
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            // Buscar usuario existente
            $user = User::where('email', $googleUser->getEmail())->first();

            if (!$user) {
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'password' => Hash::make(uniqid()),
                    'role' => 'usuario',
                ]);
            }

            // Generar JWT
            $token = JWTAuth::fromUser($user);

            // Redirigir al componente de éxito con el token
            return redirect('/oauth/success?token=' . $token);

        } catch (\Exception $e) {
            // Si hay error, redirigir al login con mensaje de error
            return redirect('/login?error=google_auth_failed');
        }
    }
}