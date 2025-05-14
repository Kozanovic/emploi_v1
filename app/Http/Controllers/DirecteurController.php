<?php

namespace App\Http\Controllers;

use App\Models\Directeur;
use App\Models\User as Utilisateur;
use Illuminate\Http\Request;

class DirecteurController extends Controller
{
    /**
     * Affiche la liste des directeurs
     */
    public function index()
    {
        $utilisateurs = Utilisateur::all();
        return response()->json([
            'data' => Directeur::with('utilisateur')->get(),
            'utilisateurs' => $utilisateurs,
        ]);
    }

    /**
     * Crée un nouveau directeur
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'utilisateur_id' => 'required|exists:utilisateurs,id|unique:directeurs'
        ]);

        $directeur = Directeur::create($data);

        return response()->json([
            'data' => $directeur->load('utilisateur')
        ], 201);
    }

    /**
     * Affiche un directeur spécifique
     */
    public function show(Directeur $directeur)
    {
        return response()->json([
            'data' => $directeur->load(['utilisateur'])
        ]);
    }

    /**
     * Met à jour un directeur
     */
    public function update(Request $request, Directeur $directeur)
    {
        $data = $request->validate([
            'utilisateur_id' => 'sometimes|exists:utilisateurs,id|unique:directeurs,utilisateur_id,' . $directeur->id
        ]);

        $directeur->update($data);

        return response()->json([
            'data' => $directeur->fresh('utilisateur')
        ]);
    }

    /**
     * Supprime un directeur
     */
    public function destroy(Directeur $directeur)
    {
        $directeur->delete();
        return response()->json([
            'message' => 'Directeur supprimé avec succès.'
        ], 204);
    }
}
