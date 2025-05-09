<?php

// app/Http/Controllers/UtilisateurController.php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Response;

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
        $utilisateur->update($validated);

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
            'password' => bcrypt($validated['password']),
            'role' => $validated['role'],
        ]);

        return response()->json([
            'message' => 'Utilisateur créé avec succès.',
            'data' => $utilisateur
        ]);
    }

    public function login(Request $request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Email ou mot de passe incorrect.',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $utilisateur = Auth::user();
        $token = $utilisateur->createToken('token')->plainTextToken;

        $cookie = cookie('jwt', $token, 60 * 24); // 1 jour
        return response()->json([
            'message' => $token,
            'data' => $utilisateur,
        ])->withCookie($cookie);
    }

    public function logout(){
        $cookie = Cookie::forget('jwt');
        return response()->json([
            'message' => 'Déconnexion réussie.'
        ])->withCookie($cookie);
    }
}
