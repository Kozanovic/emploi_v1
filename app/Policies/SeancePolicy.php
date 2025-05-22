<?php

namespace App\Policies;

use App\Models\User;

class SeancePolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }
    public function create(User $user): bool
    {
        // Seul le DirecteurEtablissement peut créer un module
        return $user->estDirecteurEtablissement();
    }
    public function update(User $user): bool
    {
        return $user->estDirecteurEtablissement();
    }
    public function delete(User $user): bool
    {
        return $user->estDirecteurEtablissement();
    }
    public function view(User $user): bool
    {
        return $user->estDirecteurEtablissement() || $user->estFormateur();
    }
    public function viewAny(User $user): bool
    {
        return $user->estDirecteurEtablissement() || $user->estFormateur();
    }
}
