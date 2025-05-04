<?php

namespace Database\Factories;

use App\Models\Ferie;
use App\Models\Semaine;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SemFer>
 */
class SemFerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $semaine = Semaine::inRandomOrder()->first();
        $ferie = Ferie::inRandomOrder()->first();
        return [
            'ferie_id' => $ferie->id,
            'semaine_id' => $semaine->id,
        ];
    }
}
