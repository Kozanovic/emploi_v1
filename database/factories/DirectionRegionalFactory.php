<?php

namespace Database\Factories;

use App\Models\DirecteurRegional;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\DirectionRegional;

class DirectionRegionalFactory extends Factory
{
    protected $model = DirectionRegional::class;

    public function definition()
    {
        $directeur_regionals = DirecteurRegional::inRandomOrder()->first();
        return [
            'nom' => $this->faker->company(),
            'adresse' => $this->faker->address(),
            'telephone' => $this->faker->phoneNumber(),
            'directeur_regional_id' => $directeur_regionals->id
        ];
    }
}
