<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Incidencias;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\voto>
 */
class VotosFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'fecha' => $this->faker->dateTimeThisYear(),
            'user_id' => User::factory(),
            'incidencia_id' => Incidencias::factory(),
        ];
    }
}
