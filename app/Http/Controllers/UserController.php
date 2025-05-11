<?php

// app/Http/Controllers/UtilisateurController.php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Afficher la liste des utilisateurs.
     */
    public function index()
    {
        $utilisateurs = User::all();
        $roles = [
            'Directeur',
            'Formateur',
        ];
        return response()->json([
            'message' => 'Liste des utilisateurs récupérée avec succès.',
            'data' => $utilisateurs,
            'roles' => $roles
        ]);
    }

    /**
     * Créer un utilisateur.
     */
    public function store(Request $request)
    {
        // Validation des champs nécessaires
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => 'required|in:Directeur,Formateur',
        ]);

        // Création de l'utilisateur après validation
        $utilisateur = User::create([
            'nom' => $validated['nom'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'role' => $validated['role'],
        ]);

        return response()->json([
            'message' => 'Utilisateur créé avec succès.',
            'data' => $utilisateur
        ]);
    }

    /**
     * Afficher les détails d'un utilisateur.
     */
    public function show($id)
    {
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
            'role' => 'sometimes|required|in:Directeur,Formateur',
        ]);

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
            'role' => 'required|in:Directeur,Formateur',
        ]);

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
    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());
        return response()->json([
            'message' => 'Déconnexion réussie.'
        ]);
    }

    public function getUser(Request $request)
    {
        $utilisateur = $request->attributes->get('user');

        if (!$utilisateur) {
            return response()->json(['message' => 'Utilisateur non authentifié'], 401);
        }

        return response()->json($utilisateur);
    }
}
