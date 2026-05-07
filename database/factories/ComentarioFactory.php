<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Incidencias;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\comentarios>
 */
class ComentarioFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'descripcion' => $this->faker->paragraph(),
            'fecha' => $this->faker->dateTimeThisYear(),
            'user_id' => User::factory(), // Crea un usuario si no existe o usa uno existente
            'incidencia_id' => Incidencias::factory(), // Crea una incidencia si no existe o usa una existente
        ];
    }
}
