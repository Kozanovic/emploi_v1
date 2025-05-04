<?php

namespace Database\Factories;

use App\Models\Groupe;
use App\Models\Module;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Suivre>
 */
class SuivreFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $module = Module::inRandomOrder()->first();
        $groupe = Groupe::inRandomOrder()->first();
        return [
            'heure_effectue' => $this->faker->numberBetween(1, 120),
            'module_id' => $module->id,
            'groupe_id' => $groupe->id,
        ];
    }
}
