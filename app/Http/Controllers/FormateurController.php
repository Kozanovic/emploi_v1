<?php

namespace App\Http\Controllers;

use App\Models\Formateur;
use App\Models\Utilisateur;
use Illuminate\Http\Request;

class FormateurController extends Controller
{
    /**
     * Affiche la liste des formateurs
     */
    public function index()
    {
        $utilisateurs = Utilisateur::where('role', 'Formateur')->get();
        $etablissements = Formateur::with('etablissement')->get();
        $complexes = Formateur::with('complexe')->get();
        $directions = Formateur::with('direction_regional')->get();
        return response()->json([
            'data' => Formateur::with(['utilisateur', 'etablissement', 'complexe', 'direction_regional'])->get(),
            'utilisateurs' => $utilisateurs,
            'etablissements' => $etablissements,
            'complexes' => $complexes,
            'directions' => $directions,
            'message' => 'Liste des formateurs récupérée avec succès'
        ]);
    }

    /**
     * Crée un nouveau formateur
     */
    public function store(Request $request)
    {
        // Validation des champs nécessaires
        $validated = $request->validate([
            'specialite' => 'required|string|max:255',
            'heures_hebdomadaire' => 'required|integer|min:1',
            'utilisateur_id' => 'required|exists:utilisateurs,id',
            'etablissement_id' => 'required|exists:etablissements,id',
            'complexe_id' => 'required|exists:complexes,id',
            'direction_regional_id' => 'required|exists:direction_regionals,id',
        ]);
        $formateur = Formateur::create($validated);

        return response()->json([
            'message' => 'Formateur créé avec succès.',
            'data' => $formateur
        ]);
    }

    /**
     * Affiche un formateur spécifique
     */
    public function show(Formateur $formateur)
    {
        return response()->json([
            'data' => $formateur->load(['utilisateur', 'etablissement', 'complexe', 'direction_regional'])
        ]);
    }

    /**
     * Met à jour un formateur
     */
    public function update(Request $request, Formateur $formateur)
    {
        $validated = $request->validate([
            'specialite' => 'sometimes|required|string|max:255',
            'heures_hebdomadaire' => 'sometimes|required|integer|min:1',
            'utilisateur_id' => 'sometimes|required|exists:utilisateurs,id',
            'etablissement_id' => 'sometimes|required|exists:etablissements,id',
            'complexe_id' => 'sometimes|required|exists:complexes,id',
            'direction_regional_id' => 'sometimes|required|exists:direction_regionals,id',
        ]);
        $formateur->update($validated);

        return response()->json([
            'message' => 'Formateur mis à jour avec succès.',
            'data' => $formateur->fresh(['utilisateur', 'etablissement', 'complexe', 'direction_regional'])
        ]);
    }
    /**
     * Supprime un formateur
     */
    public function destroy(Formateur $formateur)
    {
        $formateur->delete();
        return response()->json([
            'message' => 'Formateur supprimé avec succès.'
        ], 204);
    }
}
