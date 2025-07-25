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
    OffrirController,
    StagiaireController
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
Route::get('/getDirectionRegionale', [StagiaireController::class, 'getDirectionRegionale']);
Route::get('/getComplexe', [StagiaireController::class, 'getComplexe']);
Route::get('/getEtablissement', [StagiaireController::class, 'getEtablissement']);
Route::get('/getGroupe', [StagiaireController::class, 'getGroupe']);
Route::get('/getFiliere/{etabId}', [StagiaireController::class, 'getFiliere']); 
Route::get('/getSecteurParEtablissement/{etabId}', [StagiaireController::class, 'getSecteurParEtablissement']);
Route::get('/getSeance/{etabId}/{groupId}/{weekId}', [StagiaireController::class, 'getSeance']);
Route::get('/semaines/etablissment/{etablissementId}', [StagiaireController::class, 'getWeeks']);
Route::get('/stagiaire/export-emploi-du-temps', [StagiaireController::class, 'exportEmploiDuTempsStagiaire']);

Route::middleware([AuthJwtMiddlewaer::class])->group(function () {
    Route::get('/utilisateurs', [UserController::class, 'index']); // ✅
    Route::post('/register', [UserController::class, 'register']); // ✅
    Route::get('/utilisateurs/{id}', [UserController::class, 'show']); // ✅
    Route::put('/utilisateurs/{id}', [UserController::class, 'update']); // ✅
    Route::delete('/utilisateurs/{id}', [UserController::class, 'destroy']); // ✅;
    Route::post('/logout', [UserController::class, 'logout']); // ✅
    Route::get('/groupes-par-secteur/{secteurId}', [SectEfpController::class, 'groupesParSecteur']);
    Route::get('/filter-by-week/{semaineId}', [SemaineController::class, 'filterByWeek']);
    Route::get('/export-emploi-du-temps/{selectedSecteur}/{semaine?}', [SeanceController::class, 'exportEmploiDuTemps']);
    Route::get('/mes-seances/semaine/{weekId}', [SeanceController::class, 'getSeancesByWeek']);
    Route::get('/formateur/export-emploi-du-temps', [SeanceController::class, 'exportEmploiDuTempsFormateur']);
    Route::get('/formateurs-par-module-et-groupe/{moduleId}/{groupeId}', [AffectationController::class, 'formateursParModuleEtGroupe']);
    Route::get('/modules-par-groupe/{groupeId}', [AffectationController::class, 'getModuleByGroupe']);
    Route::get('/seances-par-semaine/{semaineId}', [SeanceController::class, 'getSeancesBySemaine']);
    Route::get('/getSecteurs', [AffectationController::class, 'getSecteurs']);
    Route::get('/getFilieresBySecteur/{secteurId}', [AffectationController::class, 'getFilieresBySecteur']);
    Route::get('/getModulesAndGroupesByFiliere/{filiereId}', [AffectationController::class, 'getModulesAndGroupesByFiliere']);

    Route::apiResources([
        // Tables principales
        'directions-regionales' => DirectionRegionalController::class, //1 - Directeur_super ✅
        'complexes' => ComplexeController::class, //2 - Directeur_Régional ✅
        'etablissements' => EtablissementController::class, //3- Directeur_complexe ✅

        // Tables de relations utilisateurs
        'directeurs-super' => directeurSuperController::class, //✅ ymkn yssawb directeurRegional
        'directeurs-regionales' => directeurRegionalController::class, //✅ ymkn yssawb formateur w directeurComplexe
        'directeurs-complexes' => directeurComplexeController::class, //✅ ymkn yssawb directeurEtablissement
        'directeur-etablissements' => directeurEtablissementController::class, //✅ maghaydir 7ta 7aja 
        'formateurs' => FormateurController::class, //✅ maghaydir 7ta 7aja 

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
