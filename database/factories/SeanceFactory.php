<?php

namespace Database\Factories;

use App\Models\Formateur;
use App\Models\Groupe;
use App\Models\Module;
use App\Models\Salle;
use App\Models\Semaine;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Seance>
 */
class SeanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $semaine = Semaine::inRandomOrder()->first();
        $salle = Salle::inRandomOrder()->first();
        $module = Module::inRandomOrder()->first();
        $formateur = Formateur::inRandomOrder()->first();
        $groupe = Groupe::inRandomOrder()->first();
        return [
            'date_seance' => $this->faker->dateTimeBetween($semaine->date_debut, $semaine->date_fin),
            'heure_debut' => $this->faker->time(),
            'heure_fin' => $this->faker->time(),
            'type' => $this->faker->randomElement(['presentiel', 'distanciel']),
            'numero_seance' => $this->faker->numberBetween(1, 10),
            'semaine_id' => $semaine->id,
            'salle_id' => $salle->id,
            'module_id' => $module->id,
            'formateur_id' => $formateur->id,
            'groupe_id' => $groupe->id,
        ];
    }
}
