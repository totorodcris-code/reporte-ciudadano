<?php

namespace Tests\Feature\Api\V1;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Test successful login
     */
    public function test_successful_login(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'access_token',
                    'token_type',
                    'expires_in',
                    'user' => [
                        'id',
                        'nombre',
                        'email',
                        'rol',
                        'perfil'
                    ]
                ]
            ])
            ->assertJson([
                'success' => true,
                'message' => 'Login exitoso',
                'data' => [
                    'token_type' => 'bearer',
                    'user' => [
                        'email' => 'test@example.com',
                        'rol' => 'usuario'
                    ]
                ]
            ]);
    }

    /**
     * Test login with invalid credentials
     */
    public function test_login_with_invalid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Credenciales incorrectas',
                'error_code' => 'INVALID_CREDENTIALS'
            ]);
    }

    /**
     * Test login with non-existent user
     */
    public function test_login_with_non_existent_user(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Error de validación'
            ]);
    }

    /**
     * Test successful registration
     */
    public function test_successful_registration(): void
    {
        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson('/api/v1/auth/register', $userData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'access_token',
                    'token_type',
                    'expires_in',
                    'user' => [
                        'id',
                        'nombre',
                        'email',
                        'rol',
                        'perfil'
                    ]
                ]
            ])
            ->assertJson([
                'success' => true,
                'message' => 'Usuario registrado exitosamente',
                'data' => [
                    'user' => [
                        'nombre' => 'John Doe',
                        'email' => 'john@example.com',
                        'rol' => 'usuario'
                    ]
                ]
            ]);

        // Verify user was created in database
        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
            'name' => 'John Doe',
            'role' => 'usuario'
        ]);
    }

    /**
     * Test registration with invalid data
     */
    public function test_registration_with_invalid_data(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => '',
            'email' => 'invalid-email',
            'password' => '123',
            'password_confirmation' => '456',
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'success',
                'message',
                'errors'
            ])
            ->assertJson([
                'success' => false,
                'message' => 'Error de validación'
            ]);
    }

    /**
     * Test getting authenticated user profile
     */
    public function test_get_authenticated_user_profile(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $token = JWTAuth::fromUser($user);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/v1/auth/me');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'nombre',
                    'email',
                    'rol',
                    'perfil',
                    'estadisticas'
                ]
            ])
            ->assertJson([
                'success' => true,
                'message' => 'Perfil obtenido exitosamente',
                'data' => [
                    'nombre' => 'Test User',
                    'email' => 'test@example.com',
                    'rol' => 'usuario'
                ]
            ]);
    }

    /**
     * Test getting profile without authentication
     */
    public function test_get_profile_without_authentication(): void
    {
        $response = $this->getJson('/api/v1/auth/me');

        $response->assertStatus(401);
    }

    /**
     * Test successful logout
     */
    public function test_successful_logout(): void
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/auth/logout');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Sesión cerrada exitosamente'
            ]);
    }

    /**
     * Test logout without authentication
     */
    public function test_logout_without_authentication(): void
    {
        $response = $this->postJson('/api/v1/auth/logout');

        $response->assertStatus(401);
    }

    /**
     * Test token refresh
     */
    public function test_token_refresh(): void
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/auth/refresh');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'access_token',
                    'token_type',
                    'expires_in',
                    'user'
                ]
            ])
            ->assertJson([
                'success' => true,
                'message' => 'Token refrescado exitosamente',
                'data' => [
                    'token_type' => 'bearer'
                ]
            ]);
    }

    /**
     * Test profile update
     */
    public function test_profile_update(): void
    {
        $user = User::factory()->create([
            'name' => 'Original Name',
            'email' => 'original@example.com',
        ]);
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/v1/auth/profile', [
                'name' => 'Updated Name',
                'email' => 'updated@example.com',
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'nombre',
                    'email',
                    'rol',
                    'updated_at'
                ]
            ])
            ->assertJson([
                'success' => true,
                'message' => 'Perfil actualizado exitosamente',
                'data' => [
                    'nombre' => 'Updated Name',
                    'email' => 'updated@example.com'
                ]
            ]);

        // Verify user was updated in database
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
            'email' => 'updated@example.com'
        ]);
    }

    /**
     * Test profile update with invalid data
     */
    public function test_profile_update_with_invalid_data(): void
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/v1/auth/profile', [
                'email' => 'invalid-email',
                'password' => '123',
                'password_confirmation' => '456',
            ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Error de validación'
            ]);
    }
}
