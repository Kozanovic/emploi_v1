<?php

namespace Database\Seeders;

use App\Models\Affectation;
use Illuminate\Database\Seeder;

use App\Models\AnneeScolaire;
use App\Models\directeurEtablissement;
use App\Models\DirecteurSuper;
use App\Models\DirecteurComplexe;
use App\Models\DirecteurRegional;
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
        Utilisateur::factory()->create([
            'nom' => 'mohammed',
            'email' => 'mohammed@gmail.com',
            'password' => Hash::make('AQZSEDRF'),
            'role' => 'DirecteurEtablissement',
        ]);
        Utilisateur::factory()->create([
            'nom' => 'amine',
            'email' => 'amine@gmail.com',
            'password' => Hash::make('AQZSEDRF'),
            'role' => 'DirecteurSuper',
        ]);
        Utilisateur::factory()->create([
            'nom' => 'sanaa',
            'email' => 'sanaa@gmail.com',
            'password' => Hash::make('AQZSEDRF'),
            'role' => 'DirecteurRegional',
        ]);
        Utilisateur::factory()->create([
            'nom' => 'outhman',
            'email' => 'outhman@gmail.com',
            'password' => Hash::make('AQZSEDRF'),
            'role' => 'DirecteurComplexe',
        ]);
        Utilisateur::factory()->create([
            'nom' => 'rayan',
            'email' => 'rayan@gmail.com',
            'password' => Hash::make('AQZSEDRF'),
            'role' => 'Formateur',
        ]);
        Utilisateur::factory()->create([
            'nom' => 'salsabil',
            'email' => 'salsabil@gmail.com',
            'password' => Hash::make('AQZSEDRF'),
            'role' => 'Formateur',
        ]);
        DirecteurEtablissement::factory(5)->create();
        DirecteurSuper::factory(5)->create();
        DirecteurComplexe::factory(5)->create();
        DirecteurRegional::factory(5)->create();
        DirectionRegional::factory(5)->create();
        Complexe::factory(5)->create();
        Etablissement::factory(5)->create();
        Formateur::factory(5)->create();
        Secteur::factory(5)->create();
        Filiere::factory(5)->create();
        Groupe::factory(5)->create();
        Module::factory(5)->create();
        AnneeScolaire::factory(5)->create();
        Salle::factory(5)->create();
        Semaine::factory(5)->create();
        Ferie::factory(5)->create();
        Seance::factory(5)->create();
        Affectation::factory(5)->create();
        Offrir::factory(5)->create();
        SectEfp::factory(5)->create();
        SemFer::factory(5)->create();
        Suivre::factory(5)->create();
    }
}
