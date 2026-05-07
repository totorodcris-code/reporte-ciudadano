<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\voto;
use App\Models\User;
use App\Models\Incidencias;

class VotoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Asegurarse de que existen usuarios e incidencias antes de crear votos
        $this->call([
            UserSeeder::class,
            IncidenciaSeeder::class,
        ]);

        voto::create([
            'user_id' => User::first()->id,
            'incidencia_id' => Incidencias::first()->id
        ]);

        voto::create([
            'user_id' => User::skip(1)->first()->id ?? User::first()->id,
            'incidencia_id' => Incidencias::first()->id
        ]);

        voto::create([
            'user_id' => User::first()->id,
            'incidencia_id' => Incidencias::skip(1)->first()->id ?? Incidencias::first()->id
        ]);
    }
}