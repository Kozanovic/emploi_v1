<?php

namespace App\Http\Controllers;

use App\Models\Directeur;
use Illuminate\Http\Request;

class DirecteurController extends Controller
{
    /**
     * Affiche la liste des directeurs
     */
    public function index()
    {
        $directeurs = Directeur::with('utilisateur')->get();
        return response()->json([
            'message' => 'Liste des directeurs récupérée avec succès.',
            'data' => $directeurs,
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
            'message' => 'Directeur créé avec succès.',
            'data' => $directeur->load('utilisateur'),
        ], 201);
    }

    /**
     * Affiche un directeur spécifique
     */
    public function show(Directeur $directeur)
    {
        return response()->json([
            'message' => 'Directeur récupéré avec succès.',
            'data' => $directeur->load(['utilisateur']),
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
            'message' => 'Directeur mis à jour avec succès.',
            'data' => $directeur->fresh('utilisateur'),
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
