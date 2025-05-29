<?php

namespace App\Http\Controllers;

use App\Models\DirectionRegional;
use App\Models\Etablissement;
use App\Models\Formateur;
use App\Models\Secteur;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;


class FormateurController extends Controller
{
    /**
     * Affiche la liste des formateurs
     */
    public function index()
    {
        // Vérification de l'autorisation
        $currentUser = Auth::user();
        if (!Gate::forUser($currentUser)->allows('view', Formateur::class)) {
            return response()->json([
                'message' => 'Vous n\'êtes pas autorisé à voir les formateurs.',
            ], 403);
        }
        $formateurs = Formateur::with(['utilisateur', 'etablissement', 'complexe', 'direction_regional'])->get();
        $secteurs = Secteur::all();
        $directionRegionales = DirectionRegional::all();
        $etablissements = Etablissement::where('direction_regional_id', $currentUser->direction_regional_id)->get();
        $utilisateurs = User::where('role', 'Formateur')->get();
        return response()->json([
            'message' => 'Liste des formateurs récupérée avec succès',
            'data' => $formateurs,
            'secteurs' => $secteurs,
            'direction_regionales' => $directionRegionales,
            'etablissements' => $etablissements,
            'utilisateurs' => $utilisateurs,
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
        // Vérification de l'autorisation
        $currentUser = Auth::user();
        if (!Gate::forUser($currentUser)->allows('create', Formateur::class)) {
            return response()->json([
                'message' => 'Vous n\'êtes pas autorisé à créer un formateur.',
            ], 403);
        }
        $formateur = Formateur::create($validated);

        return response()->json([
            'message' => 'Formateur créé avec succès.',
            'data' => $formateur
        ]);
    }

    /**
     * Affiche un formateur spécifique
     */
    public function show($id)
    {
        $formateur = Formateur::findOrFail($id);
        // Vérification de l'autorisation
        $currentUser = Auth::user();
        if (!Gate::forUser($currentUser)->allows('viewAny', $formateur)) {
            return response()->json([
                'message' => 'Vous n\'êtes pas autorisé à voir ce formateur.',
            ], 403);
        }
        return response()->json([
            'message' => 'details du formateur',
            'data' => $formateur->load(['utilisateur', 'etablissement', 'complexe', 'direction_regional']),
        ]);
    }

    /**
     * Met à jour un formateur
     */
    public function update(Request $request, $id)
    {
        $formateur = Formateur::findOrFail($id);
        $validated = $request->validate([
            'specialite' => 'sometimes|required|string|max:255',
            'heures_hebdomadaire' => 'sometimes|required|integer|min:1',
            'utilisateur_id' => 'sometimes|required|exists:utilisateurs,id',
            'etablissement_id' => 'sometimes|required|exists:etablissements,id',
            'complexe_id' => 'sometimes|required|exists:complexes,id',
            'direction_regional_id' => 'sometimes|required|exists:direction_regionals,id',
        ]);

        // Vérification de l'autorisation
        $currentUser = Auth::user();
        if (!Gate::forUser($currentUser)->allows('update', $formateur)) {
            return response()->json([
                'message' => 'Vous n\'êtes pas autorisé à mettre à jour ce formateur.',
            ], 403);
        }

        //verifier si le formateur peut gerer une seance est vrai puis changer le role de l'utilisateur a DirecteurEtablissement
        if ($validated['peut_gerer_seance'] == true) {
            $formateur->utilisateur->update(['role' => 'DirecteurEtablissement']);
        } else {
            $formateur->utilisateur->update(['role' => 'Formateur']);
        }
        $formateur->update($validated);

        return response()->json([
            'message' => 'Formateur mis à jour avec succès.',
            'data' => $formateur->fresh(['utilisateur', 'etablissement', 'complexe', 'direction_regional'])
        ]);
    }
    /**
     * Supprime un formateur
     */
    public function destroy($id)
    {
        $formateur = Formateur::findOrFail($id);
        // Vérification de l'autorisation
        $currentUser = Auth::user();
        if (!Gate::forUser($currentUser)->allows('delete', $formateur)) {
            return response()->json([
                'message' => 'Vous n\'êtes pas autorisé à supprimer ce formateur.',
            ], 403);
        }
        $formateur->delete();
        return response()->json([
            'message' => 'Formateur supprimé avec succès.'
        ], 204);
    }
}
