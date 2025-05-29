<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User as Utilisateur;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    protected $model = Utilisateur::class;

    public function definition()
    {
        return [
            'nom' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => Hash::make('password'),
            'role' => $this->faker->randomElement(['DirecteurSuper', 'DirecteurRegional', 'DirecteurComplexe', 'DirecteurEtablissement', 'Formateur',"Stagiaire"]),
        ];
    }
}
