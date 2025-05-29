<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{
    /**
     * Retourne les rôles subordonnés selon le rôle de l'utilisateur actuel.
     */
    private function getSubordinateRoles(string $role): array
    {
        return match ($role) {
            'DirecteurSuper' => ['DirecteurRegional'],
            'DirecteurRegional' => ['DirecteurComplexe', 'Formateur'],
            'DirecteurComplexe' => ['DirecteurEtablissement', 'Formateur'],
            'DirecteurEtablissement' => ['Formateur'],
            default => [],
        };
    }

    /**
     * Afficher la liste des utilisateurs accessibles.
     */
    public function index()
    {
        $user = Auth::user();
        $subordinateRoles = $this->getSubordinateRoles($user->role);

        if (empty($subordinateRoles)) {
            return response()->json([
                'message' => "Vous n'avez pas le droit de voir la liste des utilisateurs.",
            ], 403);
        }

        $utilisateurs = User::whereIn('role', $subordinateRoles)->get();

        return response()->json([
            'message' => 'Liste des utilisateurs récupérée avec succès.',
            'data' => $utilisateurs,
            'roles' => $subordinateRoles
        ]);
    }

    /**
     * Afficher les détails d'un utilisateur s'il est subordonné.
     */
    public function show($id)
    {
        $currentUser = Auth::user();
        $targetUser = User::findOrFail($id);

        if (!in_array($targetUser->role, $this->getSubordinateRoles($currentUser->role))) {
            return response()->json(['message' => 'Accès non autorisé.'], 403);
        }

        return response()->json([
            'message' => 'Utilisateur trouvé avec succès.',
            'data' => $targetUser
        ]);
    }

    /**
     * Mettre à jour un utilisateur s'il est subordonné.
     */
    public function update(Request $request, $id)
    {
        $currentUser = Auth::user();
        $targetUser = User::findOrFail($id);

        if (!in_array($targetUser->role, $this->getSubordinateRoles($currentUser->role))) {
            return response()->json(['message' => 'Vous ne pouvez pas modifier cet utilisateur.'], 403);
        }

        $validated = $request->validate([
            'nom' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . $id,
            'password' => 'sometimes|required|string|min:8',
            'role' => 'sometimes|required|in:DirecteurSuper,DirecteurComplexe,DirecteurRegional,DirecteurEtablissement,Formateur,Stagiaire',
        ]);

        $targetUser->update([
            'nom' => $validated['nom'] ?? $targetUser->nom,
            'email' => $validated['email'] ?? $targetUser->email,
            'password' => isset($validated['password']) ? Hash::make($validated['password']) : $targetUser->password,
            'role' => $validated['role'] ?? $targetUser->role,
        ]);

        return response()->json([
            'message' => 'Utilisateur mis à jour avec succès.',
            'data' => $targetUser
        ]);
    }

    /**
     * Supprimer un utilisateur s'il est subordonné.
     */
    public function destroy($id)
    {
        $currentUser = Auth::user();
        $targetUser = User::findOrFail($id);

        if (!in_array($targetUser->role, $this->getSubordinateRoles($currentUser->role))) {
            return response()->json(['message' => 'Vous ne pouvez pas supprimer cet utilisateur.'], 403);
        }

        $targetUser->delete();

        return response()->json([
            'message' => 'Utilisateur supprimé avec succès.'
        ]);
    }

    /**
     * Enregistrer un nouvel utilisateur avec un rôle autorisé.
     */
    public function register(Request $request)
    {
        $currentUser = Auth::user();

        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => 'required|in:DirecteurSuper,DirecteurComplexe,DirecteurRegional,DirecteurEtablissement,Formateur,Stagiaire',
        ]);

        if (!in_array($validated['role'], $this->getSubordinateRoles($currentUser->role))) {
            return response()->json(['message' => 'Vous ne pouvez pas créer un utilisateur avec ce rôle.'], 403);
        }

        $utilisateur = User::create([
            'nom' => $validated['nom'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        $token = JWTAuth::fromUser($utilisateur);

        return response()->json([
            'message' => "Utilisateur créé avec succès.",
            'utilisateur' => $utilisateur,
            'token' => $token
        ], 201);
    }

    /**
     * Authentifier un utilisateur.
     */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        $utilisateur = User::where('email', $validated["email"])->first();

        if (!$utilisateur || !Hash::check($validated['password'], $utilisateur->password)) {
            return response()->json([
                'message' => 'Email ou mot de passe incorrect.',
            ], 401);
        }

        $token = JWTAuth::fromUser($utilisateur);

        return response()->json([
            'message' => 'Connexion réussie.',
            'utilisateur' => $utilisateur,
            'token' => $token
        ], 200);
    }

    /**
     * Déconnecter un utilisateur.
     */
    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());
        return response()->json(['message' => 'Déconnexion réussie.']);
    }
}
