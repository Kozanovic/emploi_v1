<?php

namespace App\Http\Controllers;

use App\Models\DirectionRegional;
use App\Models\Etablissement;
use App\Models\Formateur;
use App\Models\Complexe;
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
        $user = Auth::user();

        if (!Gate::forUser($user)->allows('view', Formateur::class)) {
            return response()->json([
                'message' => 'Non autorisé à voir les formateurs.',
            ], 403);
        }

        $formateurs = collect();
        $utilisateurs = collect();
        $etablissements = collect();
        $complexes = collect();

        if ($user->role == 'DirecteurRegional') {
            $directionRegional = $user->directeurRegional;

            if ($directionRegional) {
                $formateurs = Formateur::with(['utilisateur', 'etablissement', 'complexe', 'direction_regional'])
                    ->where('direction_regional_id', $directionRegional->id)
                    ->get();

                $utilisateurs = User::whereHas('formateur', function ($query) use ($directionRegional) {
                    $query->where('direction_regional_id', $directionRegional->id);
                })->where('role', 'Formateur')->get();

                $etablissements = Etablissement::whereHas('complexe', function ($query) use ($directionRegional) {
                    $query->where('direction_regional_id', $directionRegional->id);
                })->get();
                $complexes = Complexe::where('direction_regional_id', $directionRegional->id)->get();
            }
        }

        if ($user->role == 'DirecteurEtablissement') {
            $directeurEtab = $user->directeurEtablissement;

            if ($directeurEtab) {
                // On récupère l'établissement dirigé par ce directeur
                $etablissement = Etablissement::where('directeur_etablissement_id', $directeurEtab->id)->first();

                if ($etablissement) {
                    $formateurs = Formateur::with(['utilisateur', 'etablissement', 'complexe', 'direction_regional'])
                        ->where('etablissement_id', $etablissement->id)
                        ->get();

                    $utilisateurs = User::whereHas('formateur', function ($query) use ($etablissement) {
                        $query->where('etablissement_id', $etablissement->id);
                    })->where('role', 'Formateur')->get();
                }
                $complexes = Complexe::where('direction_regional_id', $directionRegional->id)->get();
            }
        }

        $directionRegionales = DirectionRegional::all();

        return response()->json([
            'message' => 'Liste des formateurs récupérée avec succès',
            'data' => $formateurs,
            'complexes' => $complexes,
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
            'utilisateur_id' => 'required|exists:users,id',
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
            'utilisateur_id' => 'sometimes|required|exists:users,id',
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

        if($currentUser->role == 'DirecteurRegional'){
            $formateur->update($validated);
        }else{
            if ($validated['peut_gerer_seance'] == true) {
                $formateur->utilisateur->update(['role' => 'DirecteurEtablissement']);
            } else {
                $formateur->utilisateur->update(['role' => 'Formateur']);
            }
            $formateur->update($validated);
        }

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
