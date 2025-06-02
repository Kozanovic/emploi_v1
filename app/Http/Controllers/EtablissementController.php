<?php

namespace App\Http\Controllers;

use App\Models\Etablissement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Models\DirecteurEtablissement;
use App\Models\Complexe;

class EtablissementController extends Controller
{
    /**
     * Affiche la liste des établissements
     */
    public function index()
    {
        $currentUser = Auth::user();

        if (!Gate::forUser($currentUser)->allows('view', Etablissement::class)) {
            return response()->json(['message' => 'Non autorisé.'], 403);
        }

        $directeurComplexe = $currentUser->directeurComplexe;

        if (!$directeurComplexe || !$directeurComplexe->complexe) {
            return response()->json([
                'message' => "Aucun complexe trouvé pour ce directeur.",
            ], 404);
        }

        $complexe = $directeurComplexe->complexe()
            ->with([
                'etablissements.directeurEtablissement.utilisateur'
            ])
            ->first();

        $directeurs = DirecteurEtablissement::with('utilisateur')
            ->whereHas('etablissement', function ($query) use ($complexe) {
                $query->where('complexe_id', $complexe->id);
            })
            ->get();

        return response()->json([
            'message' => 'Données récupérées avec succès.',
            'complexe' => $complexe,
            'etablissements' => $complexe->etablissements,
            'directeurs' => $directeurs,
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
            'directeur_etablissement_id' => 'required',
            'complexe_id' => 'required|exists:complexes,id',
        ]);
        // Vérification des autorisations
        if (!Gate::allows('create', Etablissement::class)) {
            return response()->json(['message' => 'Non autorisé à créer un établissement.'], 403);
        }
        // Création de l'établissement

        $etablissement = Etablissement::create($data);

        return response()->json([
            'message' => 'Etablissement créé avec succès',
            'data' => $etablissement->load(['directeurEtablissement']),
        ], 201);
    }

    /**
     * Affiche un établissement spécifique
     */
    public function show($id)
    {
        $etablissement = Etablissement::findOrFail($id);
        // Vérification des autorisations
        if (!Gate::allows('view', $etablissement)) {
            return response()->json(['message' => 'Non autorisé à voir cet établissement.'], 403);
        }
        // Récupération de l'établissement avec le directeur
        return response()->json([
            'message' => 'Etablissement récupéré avec succès',
            'data' => $etablissement->load(['directeurEtablissement']),
        ]);
    }

    /**
     * Met à jour un établissement
     */
    public function update(Request $request, $id)
    {
        $etablissement = Etablissement::findOrFail($id);
        $data = $request->validate([
            'nom' => 'sometimes|string|max:255',
            'adresse' => 'sometimes|string',
            'telephone' => 'sometimes|string',
            'directeur_etablissement_id' => 'sometimes|required',
            'complexe_id' => 'sometimes|exists:complexes,id',
        ]);
        // Vérification des autorisations
        if (!Gate::allows('update', $etablissement)) {
            return response()->json(['message' => 'Non autorisé à mettre à jour cet établissement.'], 403);
        }
        // Mise à jour de l'établissement

        $etablissement->update($data);

        return response()->json([
            'message' => 'Etablissement mis à jour avec succès',
            'data' => $etablissement->fresh('directeurEtablissement'),
        ]);
    }

    /**
     * Supprime un établissement
     */
    public function destroy($id)
    {
        $etablissement = Etablissement::findOrFail($id);
        // Vérification des autorisations
        if (!Gate::allows('delete', $etablissement)) {
            return response()->json(['message' => 'Non autorisé à supprimer cet établissement.'], 403);
        }
        $etablissement->delete();

        return response()->json(null, 204);
    }
}
