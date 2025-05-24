<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    UserController,
    DirectionRegionalController,
    ComplexeController,
    EtablissementController,
    directeurEtablissementController,
    directeurSuperController,
    directeurComplexeController,
    directeurRegionalController,
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
use App\Http\Middleware\AuthJwtMiddlewaer;

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


Route::post('/login', [UserController::class, 'login']);

Route::middleware([AuthJwtMiddlewaer::class])->group(function () {
    Route::get('/utilisateurs', [UserController::class, 'index']); // ✅
    Route::post('/register', [UserController::class, 'register']); // ✅
    Route::get('/utilisateurs/{id}', [UserController::class, 'show']); // ✅
    Route::put('/utilisateurs/{id}', [UserController::class, 'update']); // ✅
    Route::delete('/utilisateurs/{id}', [UserController::class, 'destroy']); // ✅;
    Route::get('/groupes-par-secteur/{secteurId}', [GroupeController::class, 'getGroupesBySecteur']);// ✅

    Route::apiResources([
        // Tables principales
        'directions-regionales' => DirectionRegionalController::class, //1 - Directeur_super ✅
        'complexes' => ComplexeController::class, //2 - Directeur_Régional ✅
        'etablissements' => EtablissementController::class, //3- Directeur_complexe ✅

        // Tables de relations utilisateurs
        'directeurs-super' => directeurSuperController::class, //✅ ymkn yssawb directeurRegional
        'directeurs-regionales' => directeurRegionalController::class, //✅ ymkn yssawb formateur w directeurComplexe
        'directeurs-complexes' => directeurComplexeController::class, //✅ ymkn yssawb directeurEtablissement
        'directeur-etablissements' => directeurEtablissementController::class, //✅ maghaydir 7ta 9lwa 
        'formateurs' => FormateurController::class, //✅ maghaydir 7ta 9lwa 

        // Tables métiers
        'secteurs' => SecteurController::class, //1 - Directeur_super ✅
        'filieres' => FiliereController::class, //1 - Directeur_super ✅
        'groupes' => GroupeController::class, //4 - Directeur_établissement ✅
        'modules' => ModuleController::class, //4 - Directeur_établissement ✅

        // Tables de planification
        'annees-scolaires' => AnneeScolaireController::class, //1 - Directeur_super ✅
        'feries' => FerieController::class, //1 - Directeur_super ✅
        'semaines' => SemaineController::class, //4 - Directeur_établissement ✅

        // Tables de gestion des ressources
        'salles' => SalleController::class, //4 - Directeur_établissement ✅
        'seances' => SeanceController::class, //4 - Directeur_établissement ou Formateur(dans des cas spécifiques) ✅

        // Tables de jointure/pivot
        'semaine-ferie' => SemFerController::class, // ❌
        'affectations' => AffectationController::class, // ❌
        'suivres' => SuivreController::class, // ❌
        'secteurs-etablissements' => SectEfpController::class, // ❌
        'offres-formations' => OffrirController::class // ❌
    ]);
});
