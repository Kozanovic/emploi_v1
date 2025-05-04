<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\AnneeScolaire;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Semaine>
 */
class SemaineFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $anneeScolaire = AnneeScolaire::inRandomOrder()->first();
        return [
            'numero_semaine' => $this->faker->numberBetween(1, 52),
            'date_debut' => $this->faker->dateTimeBetween('-1 year', '+1 year'),
            'date_fin' => $this->faker->dateTimeBetween('+1 year', '+2 years'),
            'annee_scolaire_id' => $anneeScolaire->id
        ];
    }
}
