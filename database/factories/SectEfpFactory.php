<?php

namespace Database\Factories;

use App\Models\Etablissement;
use App\Models\Secteur;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SectEfp>
 */
class SectEfpFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $secteur = Secteur::inRandomOrder()->first();
        $etablissement = Etablissement::inRandomOrder()->first();
        return [
            'secteur_id' => $secteur->id,
            'etablissement_id' => $etablissement->id,
        ];
    }
}
