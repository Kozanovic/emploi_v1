<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Complexe;
use App\Models\DirecteurComplexe;
use App\Models\DirectionRegional;

class ComplexeFactory extends Factory
{
    protected $model = Complexe::class;

    public function definition()
    {
        $direction = DirectionRegional::inRandomOrder()->first();
        $directeur_complexe = DirecteurComplexe::inRandomOrder()->first();

        return [
            'nom' => $this->faker->word(),
            'direction_regional_id' => $direction->id,
            'directeur_complexe_id' => $directeur_complexe->id,
        ];
    }
}
