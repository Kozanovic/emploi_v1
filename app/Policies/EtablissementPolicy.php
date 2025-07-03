<?php

namespace App\Policies;

use App\Models\User;

class EtablissementPolicy
{
    public function create(User $user): bool
    {
        return $user->estDirecteurRegional();
    }

    public function update(User $user): bool
    {
        return $user->estDirecteurRegional();
    }

    public function delete(User $user): bool
    {
        return $user->estDirecteurRegional();
    }

    public function view(User $user): bool
    {
        return $user->estDirecteurRegional();
    }
}
