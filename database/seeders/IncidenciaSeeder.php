<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Incidencias;
use App\Models\User;
use App\Models\Categoria;

class IncidenciaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Asegurarse de que existen usuarios y categorías antes de crear incidencias
        $this->call([
            UserSeeder::class,
            CategoriaSeeder::class,
        ]);

        Incidencias::create([
            'titulo' => 'Fallo en alumbrado público',
            'descripcion' => 'Las luces de la calle no están funcionando correctamente.',
            'imagen' => null,
            'latitud' => -12.04318,
            'longitud' => -77.02824,
            'direccion' => 'Av. Principal 123',
            'estado' => 'pendiente',
            'prioridad' => 2,
            'user_id' => User::first()->id,
            'categoria_id' => Categoria::where('nombre_categoria', 'alumbrado publico')->first()->id
        ]);

        Incidencias::create([
            'titulo' => 'Bache peligroso',
            'descripcion' => 'Hay un bache muy grande que representa un peligro para los conductores.',
            'imagen' => null,
            'latitud' => -12.05418,
            'longitud' => -77.03924,
            'direccion' => 'Jr. Secundario 456',
            'estado' => 'pendiente',
            'prioridad' => 3,
            'user_id' => User::first()->id,
            'categoria_id' => Categoria::where('nombre_categoria', 'baches')->first()->id
        ]);

        Incidencias::create([
            'titulo' => 'Semáforo dañado',
            'descripcion' => 'El semáforo no está cambiando de color y causa congestión vehicular.',
            'imagen' => null,
            'latitud' => -12.06518,
            'longitud' => -77.05024,
            'direccion' => 'Av. Transitada 789',
            'estado' => 'pendiente',
            'prioridad' => 3,
            'user_id' => User::first()->id,
            'categoria_id' => Categoria::where('nombre_categoria', 'semaforos')->first()->id
        ]);
    }
}