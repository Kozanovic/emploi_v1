<?php

namespace Database\Factories;

use App\Models\Etablissement;
use App\Models\Filiere;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Groupe>
 */
class GroupeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $filiere = Filiere::inRandomOrder()->first();
        $etablissement = Etablissement::inRandomOrder()->first();
        return [
            'nom' => $this->faker->word,
            'annee' => $this->faker->year,
            'filiere_id' => $filiere->id,
            'etablissement_id' => $etablissement->id,
        ];
    }
}
