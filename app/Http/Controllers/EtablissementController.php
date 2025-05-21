<?php

namespace App\Http\Controllers;

use App\Models\Etablissement;
use Illuminate\Http\Request;

class EtablissementController extends Controller
{
    /**
     * Affiche la liste des établissements
     */
    public function index()
    {
        $etablissements = Etablissement::with(['directeurEtablissement'])->get();
        return response()->json([
            'message' => 'Liste des établissements récupérée avec succès',
            'data' => $etablissements,
        ]);
    }

    /**
     * Crée un nouvel établissement
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nom' => 'required|string|max:255',
            'adresse' => 'required|string',
            'telephone' => 'required|string',
            'directeur_regional_id' => 'required|exists:users,id'
        ]);

        $etablissement = Etablissement::create($data);

        return response()->json([
            'message' => 'Etablissement créé avec succès',
            'data' => $etablissement->load(['directeurEtablissement']),
        ], 201);
    }

    /**
     * Affiche un établissement spécifique
     */
    public function show(Etablissement $etablissement)
    {
        return response()->json([
            'message' => 'Etablissement récupéré avec succès',
            'data' => $etablissement->load(['directeurEtablissement']),
        ]);
    }

    /**
     * Met à jour un établissement
     */
    public function update(Request $request, Etablissement $etablissement)
    {
        $data = $request->validate([
            'nom' => 'sometimes|string|max:255',
            'adresse' => 'sometimes|string',
            'telephone' => 'sometimes|string',
            'directeur_regional_id' => 'sometimes|exists:users,id'
        ]);

        $etablissement->update($data);

        return response()->json([
            'message' => 'Etablissement mis à jour avec succès',
            'data' => $etablissement->fresh('directeurEtablissement'),
        ]);
    }

    /**
     * Supprime un établissement
     */
    public function destroy(Etablissement $etablissement)
    {
        $etablissement->delete();

        return response()->json(null, 204);
    }
}
