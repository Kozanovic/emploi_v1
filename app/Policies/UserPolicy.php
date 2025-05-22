<?php

namespace App\Policies;

use App\Models\User;


class UserPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function create(User $authUser, string $targetRole): bool
    {
        // Rôles que l'utilisateur connecté N'A PAS le droit de créer
        $interdits = [
            'DirecteurSuper' => ['DirecteurSuper'], // Il ne peut pas créer un autre super
            'DirecteurRegional' => ['DirecteurSuper', 'DirecteurRegional'],
            'DirecteurComplexe' => ['DirecteurSuper', 'DirecteurRegional', 'DirecteurComplexe'],
            'DirecteurEtablissement' => ['DirecteurSuper', 'DirecteurRegional', 'DirecteurComplexe', 'DirecteurEtablissement'],
            'Formateur' => ['DirecteurSuper', 'DirecteurRegional', 'DirecteurComplexe', 'DirecteurEtablissement', 'Formateur'],
        ];

        return !in_array($targetRole, $interdits[$authUser->role] ?? []);
    }

    // role pour modifier le rôle de l'utilisateur
    public function update(User $authUser, string $targetRole): bool
    {
        // Rôles que l'utilisateur connecté N'A PAS le droit de modifier
        $interdits = [
            'DirecteurSuper' => ['DirecteurSuper'], // Il ne peut pas créer un autre super
            'DirecteurRegional' => ['DirecteurSuper', 'DirecteurRegional'],
            'DirecteurComplexe' => ['DirecteurSuper', 'DirecteurRegional', 'DirecteurComplexe'],
            'DirecteurEtablissement' => ['DirecteurSuper', 'DirecteurRegional', 'DirecteurComplexe', 'DirecteurEtablissement'],
            'Formateur' => ['DirecteurSuper', 'DirecteurRegional', 'DirecteurComplexe', 'DirecteurEtablissement', 'Formateur'],
        ];

        return !in_array($targetRole, $interdits[$authUser->role] ?? []);
    }
    public function creatableRoles(User $authUser): array
    {
        // Tous les rôles possibles
        $roles = ['DirecteurSuper', 'DirecteurRegional', 'DirecteurComplexe', 'DirecteurEtablissement', 'Formateur'];

        return array_filter($roles, function ($role) use ($authUser) {
            return $this->create($authUser, $role);
        });
    }
}
