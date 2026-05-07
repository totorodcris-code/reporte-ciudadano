<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear un usuario administrador por defecto si no existe
        if (!User::where('email', 'admin@example.com')->exists()) {
            User::create([
                'name' => 'Administrador',
                'email' => 'admin@example.com',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'estado' => 'activo'
            ]);
        }

        // Crear algunos usuarios adicionales
        if (!User::where('email', 'usuario1@example.com')->exists()) {
            User::create([
                'name' => 'Usuario Uno',
                'email' => 'usuario1@example.com',
                'password' => bcrypt('password'),
                'role' => 'usuario',
                'estado' => 'activo'
            ]);
        }

        if (!User::where('email', 'usuario2@example.com')->exists()) {
            User::create([
                'name' => 'Usuario Dos',
                'email' => 'usuario2@example.com',
                'password' => bcrypt('password'),
                'role' => 'usuario',
                'estado' => 'activo'
            ]);
        }
    }
}