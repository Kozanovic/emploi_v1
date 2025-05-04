<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Formateur;
use App\Models\Utilisateur;
use App\Models\Etablissement;
use App\Models\Complexe;
use App\Models\DirectionRegional;

class FormateurFactory extends Factory
{
    protected $model = Formateur::class;

    public function definition()
    {
        $utilisateur = Utilisateur::where('role', 'formateur')->inRandomOrder()->first();
        $direction = DirectionRegional::inRandomOrder()->first();
        $complexe = Complexe::inRandomOrder()->first();
        $etablissement = Etablissement::inRandomOrder()->first();

        return [
            'specialite' => $this->faker->word(),
            'heures_hebdomadaire' => $this->faker->numberBetween(10, 40),
            'utilisateur_id' => $utilisateur->id,
            'etablissement_id' => $etablissement->id,
            'complexe_id' => $complexe->id,
            'direction_regional_id' => $direction->id,
        ];
    }
}
