<?php

namespace App\Policies;

use App\Models\User;

class DirectionRegionalPolicy
{
    public function create(User $user): bool
    {
        // Seul le DirecteurSuper peut créer une direction régionale
        return $user->estDirecteurSuper();
    }

    public function update(User $user): bool
    {
        return $user->estDirecteurSuper();
    }

    public function delete(User $user): bool
    {
        return $user->estDirecteurSuper();
    }

    public function view(User $user): bool
    {
        return $user->estDirecteurSuper() || $user->estDirecteurRegional();
    }
}
