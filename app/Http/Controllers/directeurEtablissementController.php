<?php

namespace App\Http\Controllers;

use App\Models\DirecteurEtablissement;
use Illuminate\Http\Request;

class directeurEtablissementController extends Controller
{
    /**
     * Affiche la liste des directeurs
     */
    public function index()
    {
        $directeursEtablissement = DirecteurEtablissement::with('utilisateur')->get();
        return response()->json([
            'message' => 'Liste des directeurs récupérée avec succès.',
            'data' => $directeursEtablissement,
        ]);
    }

    /**
     * Crée un nouveau directeur
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'utilisateur_id' => 'required|exists:utilisateurs,id|unique:directeur_regionals'
        ]);

        $directeur = DirecteurEtablissement::create($data);

        return response()->json([
            'message' => 'Directeur créé avec succès.',
            'data' => $directeur->load('utilisateur'),
        ], 201);
    }

    /**
     * Affiche un directeur spécifique
     */
    public function show($id)
    {
        $directeur = DirecteurEtablissement::findOrFail($id);
        return response()->json([
            'message' => 'Directeur récupéré avec succès.',
            'data' => $directeur->load(['utilisateur']),
        ]);
    }

    /**
     * Met à jour un directeur
     */
    public function update(Request $request,$id)
    {
        $directeur = DirecteurEtablissement::findOrFail($id);
        $data = $request->validate([
            'utilisateur_id' => 'sometimes|exists:utilisateurs,id|unique:directeur_regionals,utilisateur_id,' . $directeur->id
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
    public function destroy($id)
    {
        $directeur = DirecteurEtablissement::findOrFail($id);
        $directeur->delete();
        return response()->json([
            'message' => 'Directeur supprimé avec succès.'
        ], 204);
    }
}
