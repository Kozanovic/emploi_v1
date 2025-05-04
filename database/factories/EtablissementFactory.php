<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Etablissement;
use App\Models\Utilisateur;

class EtablissementFactory extends Factory
{
    protected $model = Etablissement::class;

    public function definition()
    {
        $directeur = Utilisateur::where('role', 'directeur')->inRandomOrder()->first();

        return [
            'nom' => $this->faker->company(),
            'adresse' => $this->faker->address(),
            'telephone' => $this->faker->phoneNumber(),
            'directeur_id' => $directeur->id,
        ];
    }
}
