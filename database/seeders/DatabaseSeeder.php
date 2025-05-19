<?php

namespace Database\Seeders;

use App\Models\Affectation;
use Illuminate\Database\Seeder;

use App\Models\AnneeScolaire;
use App\Models\Directeur;
use App\Models\Etablissement;
use App\Models\Formateur;
use App\Models\User as Utilisateur;
use App\Models\Complexe;
use App\Models\DirectionRegional;
use App\Models\Ferie;
use App\Models\Filiere;
use App\Models\Secteur;
use App\Models\Groupe;
use App\Models\Module;
use App\Models\Offrir;
use App\Models\Salle;
use App\Models\Seance;
use App\Models\SectEfp;
use App\Models\Semaine;
use App\Models\SemFer;
use App\Models\Suivre;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Utilisateur::factory()->create();
        Utilisateur::factory()->create([
            'nom' => 'mohammed',
            'email' => 'mohammed@gmail.com',
            'password' => Hash::make('AQZSEDRF'),
            'role' => 'Directeur',
        ]);
        Utilisateur::factory()->create([
            'nom' => 'rayan',
            'email' => 'rayan@gmail.com',
            'password' => Hash::make('AQZSEDRF'),
            'role' => 'Formateur',
        ]);
        DirectionRegional::factory()->create();
        Complexe::factory()->create();
        Directeur::factory()->create();
        Etablissement::factory()->create();
        Formateur::factory()->create();
        Secteur::factory()->create();
        Filiere::factory()->create();
        Groupe::factory()->create();
        Module::factory()->create();
        AnneeScolaire::factory()->create();
        Salle::factory()->create();
        Semaine::factory()->create();
        Ferie::factory()->create();
        Seance::factory()->create();
        Affectation::factory()->create();
        Offrir::factory()->create();
        SectEfp::factory()->create();
        SemFer::factory()->create();
        Suivre::factory()->create();
    }
}
