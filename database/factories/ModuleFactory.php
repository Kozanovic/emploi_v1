<?php

namespace Database\Factories;

use App\Models\Filiere;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Module>
 */
class ModuleFactory extends Factory
{

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $filiere = Filiere::inRandomOrder()->first();
        return [
            'nom' => $this->faker->word(),
            'masse_horaire_presentiel' => $this->faker->numberBetween(10, 40),
            'masse_horaire_distanciel' => $this->faker->numberBetween(10, 40),
            'type_efm' => $this->faker->randomElement(['Regional', 'Local']),
            'semestre' => $this->faker->randomElement(['S1', 'S2']),
            'filiere_id' => $filiere->id,
        ];
    }
}
