<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Complexe;
use App\Models\DirectionRegional;

class ComplexeFactory extends Factory
{
    protected $model = Complexe::class;

    public function definition()
    {
        $direction = DirectionRegional::inRandomOrder()->first();

        return [
            'nom' => $this->faker->word(),
            'direction_regional_id' => $direction->id,
        ];
    }
}
