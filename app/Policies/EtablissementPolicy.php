<?php

namespace App\Policies;

use App\Models\User;

class EtablissementPolicy
{
    public function create(User $user): bool
    {
        return $user->estDirecteurComplexe();
    }

    public function update(User $user): bool
    {
        return $user->estDirecteurComplexe();
    }

    public function delete(User $user): bool
    {
        return $user->estDirecteurComplexe();
    }

    public function view(User $user): bool
    {
        return $user->estDirecteurComplexe();
    }
}
