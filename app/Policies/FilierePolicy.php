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
        return $user->estDirecteurSuper() || $user->estDirecteurRegional();
    }
    public function update(User $user): bool
    {
        return $user->estDirecteurSuper() || $user->estDirecteurRegional();
    }
    public function delete(User $user): bool
    {
        return $user->estDirecteurSuper() || $user->estDirecteurRegional();
    }
    public function view(User $user): bool
    {
        return $user->estDirecteurSuper() || $user->estDirecteurRegional();
    }
    public function viewAny(User $user): bool
    {
        return $user->estDirecteurSuper() || $user->estDirecteurRegional();
    }
}
