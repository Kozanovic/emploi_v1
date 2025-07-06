<?php

namespace App\Http\Controllers;

use App\Models\SectEfp;
use App\Models\Groupe;
use App\Models\Secteur;
use App\Models\Module;
use App\Models\Etablissement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SectEfpController extends Controller
{
    // Dans SectEfpController.php
    public function groupesParSecteur($secteurId)
    {
        $user = Auth::user();
        if ($user->role === 'DirecteurEtablissement') {
            $userEtab = $user->directeurEtablissement->etablissement->id;
        } elseif ($user->role === 'Formateur' && $user->formateur->peut_gerer_seance) {
            $userEtab = $user->formateur->etablissement->id;
        } else {
            return response()->json([
                'message' => 'Accès non autorisé',
            ], 403);
        }

        // Vérifier que le secteur appartient bien à l'établissement
        $secteurExists = SectEfp::where('etablissement_id', $userEtab)
            ->where('secteur_id', $secteurId)
            ->exists();

        if (!$secteurExists) {
            return response()->json([
                'message' => 'Secteur non trouvé pour cet établissement',
                'data' => []
            ], 404);
        }

        // Récupérer les groupes via les filières
        $groupes = Groupe::with(['filiere'])
            ->whereHas('filiere', function ($query) use ($secteurId) {
                $query->where('secteur_id', $secteurId);
            })
            ->whereHas('filiere.etablissements', function ($query) use ($userEtab) {
                $query->where('etablissements.id', $userEtab);
            })
            ->where('etablissement_id', $userEtab)
            ->get();

        // Récupérer tous les modules du secteur
        $modules = Module::whereHas('filiere', function ($query) use ($secteurId) {
            $query->where('secteur_id', $secteurId);
        })->get();

        return response()->json([
            'message' => 'Groupes et modules récupérés avec succès',
            'data' => [
                'groupes' => $groupes,
                'modules' => $modules
            ]
        ]);
    }
    public function index()
    {
        $user = Auth::user();
        if ($user->role === 'DirecteurEtablissement') {
            $userEtab = $user->directeurEtablissement->etablissement->id;
        } elseif ($user->role === 'Formateur' && $user->formateur->peut_gerer_seance) {
            $userEtab = $user->formateur->etablissement->id;
        } else {
            return response()->json([
                'message' => 'Accès non autorisé',
            ], 403);
        }

        $sectEfps = SectEfp::with(['secteur', 'etablissement'])->where('etablissement_id', $userEtab)->get();
        $secteurs = Secteur::whereDoesntHave('etablissements')->get();
        return response()->json([
            'message' => 'Liste des associations secteur/établissement récupérée avec succès',
            'data' => $sectEfps,
            'secteurs' => $secteurs,
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $userEtab = $user->directeurEtablissement->etablissement->id;
        $validated = $request->validate([
            'secteur_id' => 'required|exists:secteurs,id',
        ]);

        $sectEfp = SectEfp::create(array_merge($validated, [
            'etablissement_id' => $userEtab,
        ]));

        return response()->json([
            'message' => 'Association créée avec succès',
            'data' => $sectEfp->fresh(['secteur', 'etablissement'])
        ]);
    }

    public function show($id)
    {
        $sectEfp = SectEfp::with(['secteur', 'etablissement'])->findOrFail($id);
        return response()->json([
            'message' => 'Association récupérée avec succès',
            'data' => $sectEfp
        ]);
    }

    public function update(Request $request, $id)
    {
        $sectEfp = SectEfp::findOrFail($id);
        $user = Auth::user();
        $userEtab = $user->directeurEtablissement->etablissement->id;
        $validated = $request->validate([
            'secteur_id' => 'required|exists:secteurs,id',
        ]);

        $sectEfp->update(array_merge($validated, [
            'etablissement_id' => $userEtab,
        ]));

        return response()->json([
            'message' => 'Association mise à jour avec succès',
            'data' => $sectEfp->fresh(['secteur', 'etablissement'])
        ]);
    }

    public function destroy($id)
    {
        $sectEfp = SectEfp::findOrFail($id);
        $sectEfp->delete();

        return response()->json(['message' => 'Association supprimée avec succès']);
    }
}
