<?php

namespace App\Http\Controllers;

use App\Models\Groupe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use App\Models\SectEfp;
use App\Models\Offrir;
use App\Models\Filiere;
use App\Models\Etablissement;
use App\Models\Secteur;

class GroupeController extends Controller
{
    public function index()
    {
        $currentUser = Auth::user();

        if (!Gate::forUser($currentUser)->allows('view', Groupe::class)) {
            return response()->json([
                'message' => "Vous n'avez pas le droit de voir la liste des groupes.",
            ], 403);
        }

        $etablissement = $currentUser->directeurEtablissement->etablissement;

        if (!$etablissement) {
            return response()->json([
                'message' => "Établissement introuvable.",
            ], 404);
        }
        $groupes = $etablissement->groupes()->with(['filiere', 'etablissement'])->orderByDesc('annee')->get();

        $filieres = $etablissement->filieres()->with('secteur')->get();

        return response()->json([
            'message' => 'Liste des groupes récupérée avec succès.',
            'data' => $groupes,
            'filieres' => $filieres,
            'etablissement' => $etablissement,
        ]);
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string',
            'annee' => 'required|in:1ére année,2éme année,3éme année',
            'filiere_id' => 'required|exists:filieres,id',
            'etablissement_id' => 'required|exists:etablissements,id',
        ]);
        // Vérifier si l'utilisateur a le droit de créer un groupe
        $currentUser = Auth::user();
        if (!Gate::forUser($currentUser)->allows('create', Groupe::class)) {
            return response()->json([
                'message' => "Vous n'avez pas le droit de créer un groupe.",
            ], 403);
        }

        if ($currentUser->role === 'DirecteurEtablissement') {
            $etablissement = $currentUser->directeurEtablissement->etablissement;
        } elseif ($currentUser->role === 'Formateur' && $currentUser->formateur->peut_gerer_seance) {
            $etablissement = $currentUser->formateur->etablissement;
        } else {
            return response()->json(['message' => 'Accès non autorisé'], 403);
        }

        $filiereOfferte = $etablissement->filieres()->where('filieres.id', $validated['filiere_id'])->exists();

        if (!$filiereOfferte) {
            return response()->json([
                'message' => "Cette filière n'est pas offerte dans votre établissement.",
            ], 403);
        }

        $groupe = Groupe::create($validated)->fresh(['filiere', 'etablissement']);

        return response()->json([
            'message' => 'Groupe créé avec succès.',
            'data' => $groupe,
        ]);
    }

    public function show($id)
    {
        $groupe = Groupe::with(['filiere', 'etablissement'])->findOrFail($id);
        // Vérifier si l'utilisateur a le droit de voir les détails d'un groupe
        $currentUser = Auth::user();
        if (!Gate::forUser($currentUser)->allows('viewAny', $groupe)) {
            return response()->json([
                'message' => "Vous n'avez pas le droit de voir les détails de ce groupe.",
            ], 403);
        }
        return response()->json([
            'message' => 'Détails du groupe récupérés avec succès.',
            'data' => $groupe,
        ]);
    }

    public function update(Request $request, $id)
    {
        $groupe = Groupe::findOrFail($id);

        $validated = $request->validate([
            'nom' => 'sometimes|required|string',
            'annee' => 'sometimes|required|in:1ére année,2éme année,3éme année',
            'filiere_id' => 'sometimes|required|exists:filieres,id',
            'etablissement_id' => 'sometimes|required|exists:etablissements,id',
        ]);

        // Vérifier si l'utilisateur a le droit de mettre à jour un groupe
        $currentUser = Auth::user();
        if (!Gate::forUser($currentUser)->allows('update', $groupe)) {
            return response()->json([
                'message' => "Vous n'avez pas le droit de mettre à jour ce groupe.",
            ], 403);
        }
        $groupe->update($validated);
        $groupe->load(['filiere', 'etablissement']);

        return response()->json([
            'message' => 'Groupe mis à jour avec succès.',
            'data' => $groupe,
        ]);
    }

    public function destroy($id)
    {
        $groupe = Groupe::findOrFail($id);
        // Vérifier si l'utilisateur a le droit de supprimer un groupe
        $currentUser = Auth::user();
        if (!Gate::forUser($currentUser)->allows('delete', $groupe)) {
            return response()->json([
                'message' => "Vous n'avez pas le droit de supprimer ce groupe.",
            ], 403);
        }
        $groupe->delete();

        return response()->json([
            'message' => 'Groupe supprimé avec succès.'
        ]);
    }
}
