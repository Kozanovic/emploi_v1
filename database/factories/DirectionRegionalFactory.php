<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\DirectionRegional;

class DirectionRegionalFactory extends Factory
{
    protected $model = DirectionRegional::class;

    public function definition()
    {
        return [
            'nom' => $this->faker->company(),
            'adresse' => $this->faker->address(),
            'telephone' => $this->faker->phoneNumber(),
        ];
    }
}
