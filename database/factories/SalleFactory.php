<?php

namespace Database\Factories;

use App\Models\Etablissement;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Salle>
 */
class SalleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $etabissement = Etablissement::inRandomOrder()->first();
        return [
            'nom' => $this->faker->word(),
            'capacite' => $this->faker->numberBetween(10, 100),
            'type' => $this->faker->randomElement(['Salle', 'Atelier']),
            'etablissement_id' => $etabissement->id,
        ];
    }
}
