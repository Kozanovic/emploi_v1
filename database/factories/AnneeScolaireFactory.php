<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

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
        return [
            'nom' => $this->faker->year() . '-' . ($this->faker->year() + 1),
            'date_debut' => $this->faker->dateTimeBetween('-1 year', '+1 year'),
            'date_fin' => $this->faker->dateTimeBetween('+1 year', '+2 years'),
        ];
    }
}
