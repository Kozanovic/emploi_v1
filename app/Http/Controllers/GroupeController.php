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
        $groupes = Groupe::with(['filiere', 'etablissement'])->get();
        // Vérifier si l'utilisateur a le droit de voir la liste des groupes
        $currentUser = Auth::user();
        if (!Gate::forUser($currentUser)->allows('view', Groupe::class)) {
            return response()->json([
                'message' => "Vous n'avez pas le droit de voir la liste des groupes.",
            ], 403);
        }
        return response()->json([
            'message' => 'Liste des groupes récupérée avec succès.',
            'data' => $groupes,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string',
            'annee' => 'required|integer',
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
            'annee' => 'sometimes|required|integer',
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
    public function filtrerParSecteurEtFiliere(Request $request)
    {
        $user = Auth::user();

        $etablissementId = $user->etablissement_id;

        if (!$etablissementId) {
            return response()->json(['message' => 'Aucun établissement associé à cet utilisateur.'], 403);
        }

        $secteurs = SectEfp::where('etablissement_id', $etablissementId)->pluck('secteur_id');

        $filieresOffertes = Offrir::where('etablissement_id', $etablissementId)->pluck('filiere_id');

        if ($request->has('secteur_id')) {
            $secteurId = $request->input('secteur_id');

            if (!$secteurs->contains($secteurId)) {
                return response()->json(['message' => 'Secteur non autorisé.'], 403);
            }

            $filieresOffertes = Filiere::whereIn('id', $filieresOffertes)
                ->where('secteur_id', $secteurId)
                ->pluck('id');
        }

        $groupes = Groupe::with(['filiere', 'etablissement'])
            ->where('etablissement_id', $etablissementId)
            ->whereIn('filiere_id', $filieresOffertes)
            ->get();

        return response()->json([
            'message' => 'Groupes filtrés récupérés avec succès.',
            'data' => $groupes
        ]);
    }
    public function getGroupesByEtablissement()
    {
        // Vérifier si l'utilisateur a le droit de voir les groupes d'un établissement
        $currentUser = Auth::user();
        if (!Gate::forUser($currentUser)->allows('viewAny', Groupe::class)) {
            return response()->json([
                'message' => "Vous n'avez pas le droit de voir les groupes de cet établissement.",
            ], 403);
        }
        $etablissementId = $currentUser->etablissement_id;
        $groupes = Groupe::where('etablissement_id', $etablissementId)->with(['filiere', 'etablissement'])->get();
        return response()->json([
            'message' => 'Groupes récupérés avec succès.',
            'data' => $groupes,
        ]);
    }
    public function getGroupesByFiliere($id)
    {
        // Vérifier si l'utilisateur a le droit de voir les groupes d'une filière
        $currentUser = Auth::user();
        if (!Gate::forUser($currentUser)->allows('viewAny', Groupe::class)) {
            return response()->json([
                'message' => "Vous n'avez pas le droit de voir les groupes de cette filière.",
            ], 403);
        }
        $groupes = Groupe::where('filiere_id', $id)->with(['filiere', 'etablissement'])->get();
        return response()->json([
            'message' => 'Groupes récupérés avec succès.',
            'data' => $groupes,
        ]);
    }
}
