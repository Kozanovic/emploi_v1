<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Policies\UserPolicy;
use App\Models\DirectionRegional;
use App\Models\Complexe;
use App\Models\Etablissement;
use App\Models\Secteur;;

use App\Policies\DirectionRegionalPolicy;
use App\Policies\ComplexePolicy;
use App\Policies\EtablissementPolicy;
use App\Policies\SecteurPolicy;
use App\Models\Filiere;
use App\Policies\FilierePolicy;
use App\Models\Groupe;
use App\Policies\GroupePolicy;
use App\Models\Module;
use App\Policies\ModulePolicy;
use App\Models\AnneeScolaire;
use App\Policies\AnneeScolairePolicy;
use App\Models\Ferie;
use App\Policies\FeriePolicy;
use App\Models\Semaine;
use App\Policies\SemainePolicy;
use App\Models\Salle;
use App\Policies\SallePolicy;
use App\Models\Seance;
use App\Policies\SeancePolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }
    protected $policies = [
        User::class => UserPolicy::class,
        DirectionRegional::class => DirectionRegionalPolicy::class,
        Complexe::class => ComplexePolicy::class,
        Etablissement::class => EtablissementPolicy::class,
        Secteur::class => SecteurPolicy::class,
        Filiere::class => FilierePolicy::class,
        Groupe::class => GroupePolicy::class,
        Module::class => ModulePolicy::class,
        AnneeScolaire::class => AnneeScolairePolicy::class,
        Ferie::class => FeriePolicy::class,
        Semaine::class => SemainePolicy::class,
        Salle::class => SallePolicy::class,
        Seance::class => SeancePolicy::class,
    ];

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
