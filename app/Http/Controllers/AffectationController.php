<?php

namespace App\Http\Controllers;

use App\Models\Affectation;
use App\Models\Formateur;
use App\Models\Groupe;
use App\Models\Module;
use App\Models\Offrir;
use App\Models\SectEfp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AffectationController extends Controller
{
    public function formateursParModuleEtGroupe($selectedModuleId, $selectedGroupeId)
    {
        $user = Auth::user();
        if ($user->role === 'DirecteurEtablissement') {
            $etabId = $user->directeurEtablissement->etablissement->id;
        } elseif ($user->role === 'Formateur') {
            $etabId = $user->formateur->etablissement->id;
        } else {
            return response()->json(['message' => 'Accès interdit'], 403);
        }

        $affectations = Affectation::where('module_id', $selectedModuleId)
            ->where('groupe_id', $selectedGroupeId)
            ->whereHas('formateur', function ($query) use ($etabId) {
                $query->where('etablissement_id', $etabId);
            })
            ->with(['formateur.utilisateur'])
            ->get();

        return response()->json([
            'message' => 'Formateurs récupérés avec succès',
            'data' => $affectations
        ]);
    }
    public function getModuleByGroupe($selectedGroupeId)
    {
        $user = Auth::user();
        if ($user->role === 'DirecteurEtablissement') {
            $etabId = $user->directeurEtablissement->etablissement->id;
        } elseif ($user->role === 'Formateur') {
            $etabId = $user->formateur->etablissement->id;
        } else {
            return response()->json(['message' => 'Accès interdit'], 403);
        }

        $affectations = Affectation::where('groupe_id', $selectedGroupeId)
            ->whereHas('formateur', function ($query) use ($etabId) {
                $query->where('etablissement_id', $etabId);
            })
            ->with(['module'])
            ->get();
        return response()->json([
            'message' => 'Modules récupérés avec succès',
            'data' => $affectations
        ]);
    }
    public function index()
    {
        $user = Auth::user();
        $etabId = $user->directeurEtablissement->etablissement->id;
        $formateurs = Formateur::with('utilisateur')->where('etablissement_id', $etabId)->get();
        $affectations = Affectation::with(['formateur.utilisateur', 'module', 'groupe'])->get();
        return response()->json([
            'message' => 'Liste des affectations récupérée avec succès',
            'data' => $affectations,
            'formateurs' => $formateurs,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'formateur_id' => 'required|exists:formateurs,id',
            'module_id' => 'required|exists:modules,id',
            'groupe_id' => 'required|exists:groupes,id',
        ]);
        $existingAffectation = Affectation::where('formateur_id', $request->formateur_id)
            ->where('module_id', $request->module_id)
            ->where('groupe_id', $request->groupe_id)
            ->exists();
        if ($existingAffectation) {
            return response()->json(['message' => 'Cette affectation existe déjà.'], 400);
        }
        $affectation = Affectation::create($request->all());

        return response()->json([
            'message' => 'Affectation créée avec succès',
            'data' => $affectation->fresh(['formateur', 'module', 'groupe'])
        ]);
    }

    public function show($id)
    {
        $affectation = Affectation::with(['formateur', 'module', 'groupe'])->findOrFail($id);

        return response()->json([
            'message' => 'Affectation récupérée avec succès',
            'data' => $affectation
        ]);
    }

    public function update(Request $request, $id)
    {
        $affectation = Affectation::findOrFail($id);
        $request->validate([
            'formateur_id' => 'required|exists:formateurs,id',
            'module_id' => 'required|exists:modules,id',
            'groupe_id' => 'required|exists:groupes,id',
        ]);
        $existingAffectation = Affectation::where('formateur_id', $request->formateur_id)
            ->where('module_id', $request->module_id)
            ->where('groupe_id', $request->groupe_id)
            ->where('id', '!=', $id)
            ->exists();
        if ($existingAffectation) {
            return response()->json(['message' => 'Cette affectation existe déjà.'], 400);
        }

        $affectation->update($request->all());

        return response()->json([
            'message' => 'Affectation mise à jour avec succès',
            'data' => $affectation->fresh(['formateur', 'module', 'groupe'])
        ]);
    }

    public function destroy($id)
    {
        $affectation = Affectation::findOrFail($id);
        $affectation->delete();

        return response()->json(['message' => 'Affectation supprimée avec succès']);
    }
    public function getSecteurs()
    {
        $user = Auth::user();
        $etabId = $user->directeurEtablissement->etablissement->id;
        $secteurs = SectEfp::with(['etablissement', 'secteur'])
            ->where('etablissement_id', $etabId)
            ->get();
        return response()->json([
            'message' => 'Secteurs récupérés avec succès',
            'data' => $secteurs
        ]);
    }
    public function getFilieresBySecteur($secteurId)
    {
        $filieres = Offrir::with(['etablissement', 'filiere'])
            ->whereHas('filiere', function ($query) use ($secteurId) {
                $query->where('secteur_id', $secteurId);
            })
            ->get();
        return response()->json([
            'message' => 'Filieres récupérées avec succès',
            'data' => $filieres,
        ]);
    }
    public function getModulesAndGroupesByFiliere($filiereId)
    {
        $modules = Module::where('filiere_id', $filiereId)->get();
        $groupes = Groupe::where('filiere_id', $filiereId)->get();
        return response()->json([
            'message' => 'Modules et groupes récupérés avec succès',
            'modules' => $modules,
            'groupes' => $groupes
        ]);
    }
}
