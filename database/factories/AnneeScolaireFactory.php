<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Etablissement;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AnneeScolaire>
 */
class AnneeScolaireFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $etablissement = Etablissement::inRandomOrder()->first();
        return [
            'nom' => $this->faker->year() . '-' . ($this->faker->year() + 1),
            'date_debut' => $this->faker->dateTimeBetween('-1 year', '+1 year'),
            'date_fin' => $this->faker->dateTimeBetween('+1 year', '+2 years'),
            'etablissement_id' => $etablissement->id,
        ];
    }
}
