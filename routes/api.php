<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    UserController,
    DirectionRegionalController,
    ComplexeController,
    EtablissementController,
    DirecteurController,
    FormateurController,
    SecteurController,
    FiliereController,
    GroupeController,
    ModuleController,
    AnneeScolaireController,
    FerieController,
    SemaineController,
    SalleController,
    SeanceController,
    SemFerController,
    AffectationController,
    SuivreController,
    SectEfpController,
    OffrirController
};

/*
|--------------------------------------------------------------------------
| API Routes pour les 20 tables
|--------------------------------------------------------------------------
|
| Routes CRUD complètes pour toutes vos tables
|
*/

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::apiResources([
    // Tables principales
    'utilisateurs' => UserController::class,
    'directions-regionales' => DirectionRegionalController::class,
    'complexes' => ComplexeController::class,
    'etablissements' => EtablissementController::class,

    // Tables de relations utilisateurs
    'directeurs' => DirecteurController::class,
    'formateurs' => FormateurController::class,

    // Tables métiers
    'secteurs' => SecteurController::class,
    'filieres' => FiliereController::class,
    'groupes' => GroupeController::class,
    'modules' => ModuleController::class,

    // Tables de planification
    'annees-scolaires' => AnneeScolaireController::class,
    'feries' => FerieController::class,
    'semaines' => SemaineController::class,

    // Tables de gestion des ressources
    'salles' => SalleController::class,
    'seances' => SeanceController::class,

    // Tables de jointure/pivot
    'semaine-ferie' => SemFerController::class,
    'affectations' => AffectationController::class,
    'suivres' => SuivreController::class,
    'secteurs-etablissements' => SectEfpController::class,
    'offres-formations' => OffrirController::class
]);

Route::post('/register', [UserController::class, 'register'])->name('register');
Route::post('/login', [UserController::class, 'login'])->name('login');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [UserController::class, 'logout']);
});