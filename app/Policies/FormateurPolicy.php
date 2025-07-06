<?php

namespace App\Policies;

use App\Models\User;

class FormateurPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }
    public function create(User $user)
    {
        return $user->estDirecteurRegional();
    }
    public function update(User $user)
    {
        return $user->estDirecteurRegional() || $user->estDirecteurEtablissement();
    }
    public function delete(User $user)
    {
        return $user->estDirecteurRegional();
    }
    public function view(User $user)
    {
        return $user->estDirecteurRegional() || $user->estDirecteurEtablissement() || ($user->estFormateur() && $user->formateur && $user->formateur->peut_gerer_seance);
    }
    public function viewAny(User $user)
    {
        return $user->estDirecteurRegional() || $user->estDirecteurEtablissement() || ($user->estFormateur() && $user->formateur && $user->formateur->peut_gerer_seance);
    }
}
