<?php

namespace App\Policies;

use App\Models\User;

class SeancePolicy
{
    public function create(User $user): bool
    {
        return $user->estDirecteurEtablissement() || ($user->estFormateur() && $user->formateur && $user->formateur->peut_gerer_seance);
    }

    public function update(User $user): bool
    {
        return $user->estDirecteurEtablissement() || ($user->estFormateur() && $user->formateur && $user->formateur->peut_gerer_seance);
    }

    public function delete(User $user): bool
    {
        return $user->estDirecteurEtablissement() || ($user->estFormateur() && $user->formateur && $user->formateur->peut_gerer_seance);
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
