<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Directeur;
use App\Models\User as Utilisateur;

class DirecteurFactory extends Factory
{
    protected $model = Directeur::class;

    public function definition()
    {
        $utilisateur = Utilisateur::where('role', 'directeur')->inRandomOrder()->first();

        return [
            'utilisateur_id' => $utilisateur->id,
        ];
    }
}
