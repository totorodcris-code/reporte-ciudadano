<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\comentarios;
use App\Models\User;
use App\Models\Incidencias;

class ComentarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Asegurarse de que existen usuarios e incidencias antes de crear comentarios
        $this->call([
            UserSeeder::class,
            IncidenciaSeeder::class,
        ]);

        comentarios::create([
            'descripcion' => 'Este es un comentario sobre la falla en el alumbrado público.',
            'user_id' => User::first()->id,
            'incidencia_id' => Incidencias::first()->id
        ]);

        comentarios::create([
            'descripcion' => 'Gracias por reportar este problema, estamos trabajando en ello.',
            'user_id' => User::skip(1)->first()->id ?? User::first()->id,
            'incidencia_id' => Incidencias::first()->id
        ]);

        comentarios::create([
            'descripcion' => 'Por favor, tengan cuidado mientras transitan por esta zona.',
            'user_id' => User::first()->id,
            'incidencia_id' => Incidencias::skip(1)->first()->id ?? Incidencias::first()->id
        ]);
    }
}