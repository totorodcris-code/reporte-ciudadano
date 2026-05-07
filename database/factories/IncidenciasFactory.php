<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Incidencias>
 */
class IncidenciasFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
                'titulo' => fake()->sentence(),
                'descripcion' => fake()->paragraph(),
                'ubicacion' => fake()->address(),
                'estado' => fake()->randomElement(['pendiente', 'en progreso', 'resuelta']),
                'user_id' => \App\Models\User::factory(),
                'categoria_id' => \App\Models\Categoria::factory(),
                
        ];
    }
}
