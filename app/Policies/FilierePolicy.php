<?php

namespace App\Policies;

use App\Models\User;

class FilierePolicy
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
        // Seul le DirecteurSuper peut créer une filière
        return $user->estDirecteurSuper() ;
    }
    public function update(User $user): bool
    {
        return $user->estDirecteurSuper() ;
    }
    public function delete(User $user): bool
    {
        return $user->estDirecteurSuper() ;
    }
    public function view(User $user): bool
    {
        return $user->estDirecteurSuper() ;
    }
    public function viewAny(User $user): bool
    {
        return $user->estDirecteurSuper() ;
    }
}
