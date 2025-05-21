<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\directeurEtablissement;
use App\Models\User as Utilisateur;

class DirecteurEtablissementFactory extends Factory
{
    protected $model = directeurEtablissement::class;

    public function definition()
    {
        $utilisateur = Utilisateur::where('role', 'DirecteurEtablissement')->inRandomOrder()->first();

        return [
            'utilisateur_id' => $utilisateur->id,
        ];
    }
}
