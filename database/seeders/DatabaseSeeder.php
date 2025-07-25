<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\User as Utilisateur;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Utilisateur::factory()->create([
            'nom' => 'amine',
            'email' => 'amine@gmail.com',
            'password' => Hash::make('AQZSEDRF'),
            'role' => 'DirecteurSuper',
        ]);
    }
}
