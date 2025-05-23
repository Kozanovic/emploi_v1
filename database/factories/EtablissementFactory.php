<?php

namespace Database\Factories;

use App\Models\Complexe;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Etablissement;
use App\Models\DirecteurEtablissement;
use App\Models\User as Utilisateur;

class EtablissementFactory extends Factory
{
    protected $model = Etablissement::class;

    public function definition()
    {
        // Ensure there is a directeur user
        $user = Utilisateur::where('role', 'DirecteurEtablissement')->inRandomOrder()->first();
        $complexe = Complexe::inRandomOrder()->first();

        // Create Directeur if not already created
        $directeur = DirecteurEtablissement::firstOrCreate(
            ['utilisateur_id' => $user->id],
            ['created_at' => now(), 'updated_at' => now()]
        );

        return [
            'nom' => $this->faker->company(),
            'adresse' => $this->faker->address(),
            'telephone' => $this->faker->phoneNumber(),
            'directeur_etablissement_id' => $directeur->id,
            'complexe_id' => $complexe->id,
        ];
    }
}
