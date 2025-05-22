<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Policies\UserPolicy;
use App\Models\DirectionRegional;
use App\Models\Complexe;
use App\Models\Etablissement;
use App\Policies\DirectionRegionalPolicy;
use App\Policies\ComplexePolicy;
use App\Policies\EtablissementPolicy;

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
    ];

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
