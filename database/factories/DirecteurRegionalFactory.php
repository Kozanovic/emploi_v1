<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User as Utilisateur;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DirecteurRegional>
 */
class DirecteurRegionalFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $utilisateur = Utilisateur::where('role', 'DirecteurRegional')->inRandomOrder()->first();

        return [
            'utilisateur_id' => $utilisateur->id,
        ];
    }
}
