<?php

namespace Database\Factories;

use App\Models\Formateur;
use App\Models\Groupe;
use App\Models\Module;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Affectation>
 */
class AffectationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $formateur = Formateur::inRandomOrder()->first();
        $module = Module::inRandomOrder()->first();
        $groupe = Groupe::inRandomOrder()->first();
        return [
            'formateur_id' => $formateur->id,
            'module_id' => $module->id,
            'groupe_id' => $groupe->id,
        ];
    }
}
