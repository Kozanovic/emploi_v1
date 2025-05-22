<?php

// app/Http/Controllers/UtilisateurController.php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use App\Policies\UserPolicy;


class UserController extends Controller
{
    /**
     * Afficher la liste des utilisateurs.
     */
    public function index()
    {
        $utilisateurs = User::all();
        $user = Auth::user();
        $policy = new UserPolicy();
        // Vérifier si l'utilisateur a le droit de voir la liste des utilisateurs
        if (!Gate::forUser($user)->allows('viewAny', User::class)) {
            return response()->json([
                'message' => "Vous n'avez pas le droit de voir la liste des utilisateurs.",
            ], 403);
        }
        return response()->json([
            'message' => 'Liste des utilisateurs récupérée avec succès.',
            'data' => $utilisateurs,
            'creatable_roles' => $policy->creatableRoles($user) // Liste dynamique
        ]);
    }
    /**
     * Afficher les détails d'un utilisateur.
     */
    public function show($id)
    {
        // Vérifier si l'utilisateur a le droit de voir les détails de l'utilisateur
        $currentUser = Auth::user();
        if (!Gate::forUser($currentUser)->allows('view', $id)) {
            return response()->json([
                'message' => "Vous n'avez pas le droit de voir les détails de cet utilisateur.",
            ], 403);
        }

        $utilisateur = User::findOrFail($id);
        return response()->json([
            'message' => 'Utilisateur trouvé avec succès.',
            'data' => $utilisateur
        ]);
    }

    /**
     * Mettre à jour un utilisateur.
     */
    public function update(Request $request, $id)
    {
        // Validation des champs nécessaires
        $validated = $request->validate([
            'nom' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . $id,
            'password' => 'sometimes|required|string|min:8',
            'role' => 'sometimes|required|in:DirecteurSuper,DirecteurComplexe,DirecteurRegional,DirecteurEtablissement,Formateur',
        ]);
        // Vérifier si l'utilisateur a le droit de mettre à jour l'utilisateur
        $currentUser = Auth::user();
        if (!Gate::forUser($currentUser)->allows('update', $id)) {
            return response()->json([
                'message' => "Vous n'avez pas le droit de mettre à jour cet utilisateur.",
            ], 403);
        }
        // Vérifier si l'utilisateur a le droit de mettre à jour le rôle

        $utilisateur = User::findOrFail($id);
        $utilisateur->update([
            'nom' => $validated['nom'] ?? $utilisateur->nom,
            'email' => $validated['email'] ?? $utilisateur->email,
            'password' => isset($validated['password']) ? Hash::make($validated['password']) : $utilisateur->password,
            'role' => $validated['role'] ?? $utilisateur->role,
        ]);

        return response()->json([
            'message' => 'Utilisateur mis à jour avec succès.',
            'data' => $utilisateur
        ]);
    }

    /**
     * Supprimer un utilisateur.
     */
    public function destroy($id)
    {
        // Vérifier si l'utilisateur a le droit de supprimer l'utilisateur
        $currentUser = Auth::user();
        if (!Gate::forUser($currentUser)->allows('delete', $id)) {
            return response()->json([
                'message' => "Vous n'avez pas le droit de supprimer cet utilisateur.",
            ], 403);
        }
        $utilisateur = User::findOrFail($id);
        $utilisateur->delete();

        return response()->json([
            'message' => 'Utilisateur supprimé avec succès.'
        ]);
    }


    public function register(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => 'required|in:DirecteurSuper,DirecteurComplexe,DirecteurRegional,DirecteurEtablissement,Formateur',
        ]);

        // Vérifier si l'utilisateur a le droit de créer un utilisateur avec le rôle spécifié
        $currentUser = Auth::user();
        if (!Gate::forUser($currentUser)->allows('create', [User::class, $validated['role']])) {
            return response()->json([
                'message' => "Vous n'avez pas le droit de créer un utilisateur avec le rôle : " . $validated['role'],
            ], 403);
        }
        $utilisateur = User::create([
            'nom' => $validated['nom'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        // crèe une token  á l‘aide de Jwt
        $token = JWTAuth::fromUser($utilisateur);

        return response()->json([
            'message' => "regsiter créé avec succès",
            'utilisateur' => $utilisateur,
            'token' => $token
        ], 201); // 201  sginifier la creation
    }

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
            ], 401); // 401 signifie non autorisé
        }

        // crèe une token  á l‘aide de Jwt
        $token = JWTAuth::fromUser($utilisateur);
        return response()->json([
            'message' => 'Connexion réussie.',
            'utilisateur' => $utilisateur,
            'token' => $token
        ], 200); // 200 signifie succès
    }
}
