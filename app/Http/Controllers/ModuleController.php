<?php

namespace App\Http\Controllers;

use App\Models\Filiere;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

class ModuleController extends Controller
{
    public function index()
    {
        $currentUser = Auth::user();
        if (!Gate::forUser($currentUser)->allows('view', Module::class)) {
            return response()->json([
                'message' => "Vous n'avez pas le droit de voir la liste des modules.",
            ], 403);
        }

        $etablissement = $currentUser->directeurEtablissement->etablissement;

        $modules = Module::whereHas('filiere', function ($query) use ($etablissement) {
            $query->whereHas('etablissements', function ($q) use ($etablissement) {
                $q->where('etablissements.id', $etablissement->id);
            });
        })->with('filiere')->get();

        $filieres = $etablissement->filieres()->with('secteur')->get();

        return response()->json([
            'message' => 'Liste des modules récupérée avec succès.',
            'data' => $modules,
            'filieres' => $filieres,
        ]);
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string',
            'masse_horaire_presentiel' => 'required|integer',
            'masse_horaire_distanciel' => 'required|integer',
            'type_efm' => 'required|in:Regional,Local',
            'semestre' => 'required|in:S1,S2',
            'filiere_id' => 'required|exists:filieres,id',
        ]);

        // Vérifier si l'utilisateur a le droit de créer un module
        $currentUser = Auth::user();
        if (!Gate::forUser($currentUser)->allows('create', Module::class)) {
            return response()->json([
                'message' => "Vous n'avez pas le droit de créer un module.",
            ], 403);
        }
        $etablissement = $currentUser->directeurEtablissement->etablissement;

        // Vérifie que la filière est bien offerte dans l'établissement du directeur
        $filiereOfferte = $etablissement->filieres()->where('filieres.id', $validated['filiere_id'])->exists();

        if (!$filiereOfferte) {
            return response()->json([
                'message' => "Cette filière n'est pas offerte dans votre établissement.",
            ], 403);
        }
        $module = Module::create($validated)->fresh(['filiere']);

        return response()->json([
            'message' => 'Module créé avec succès.',
            'data' => $module,
        ]);
    }

    public function show($id)
    {
        $module = Module::with(['filiere'])->findOrFail($id);
        // Vérifier si l'utilisateur a le droit de voir les détails d'un module
        $currentUser = Auth::user();
        if (!Gate::forUser($currentUser)->allows('viewAny', $module)) {
            return response()->json([
                'message' => "Vous n'avez pas le droit de voir les détails de ce module.",
            ], 403);
        }
        return response()->json([
            'message' => 'Détails du module récupérés avec succès.',
            'data' => $module,
        ]);
    }

    public function update(Request $request, $id)
    {
        $module = Module::findOrFail($id);

        $validated = $request->validate([
            'nom' => 'sometimes|required|string',
            'masse_horaire_presentiel' => 'sometimes|required|integer',
            'masse_horaire_distanciel' => 'sometimes|required|integer',
            'type_efm' => 'required|in:Regional,Local',
            'semestre' => 'sometimes|required|in:S1,S2',
            'filiere_id' => 'sometimes|required|exists:filieres,id',
        ]);
        // Vérifier si l'utilisateur a le droit de mettre à jour un module
        $currentUser = Auth::user();
        if (!Gate::forUser($currentUser)->allows('update', $module)) {
            return response()->json([
                'message' => "Vous n'avez pas le droit de mettre à jour ce module.",
            ], 403);
        }

        $module->update($validated);
        $module->load(['filiere']);

        return response()->json([
            'message' => 'Module mis à jour avec succès.',
            'data' => $module,
        ]);
    }

    public function destroy($id)
    {
        $module = Module::findOrFail($id);
        // Vérifier si l'utilisateur a le droit de supprimer un module
        $currentUser = Auth::user();
        if (!Gate::forUser($currentUser)->allows('delete', $module)) {
            return response()->json([
                'message' => "Vous n'avez pas le droit de supprimer ce module.",
            ], 403);
        }
        $module->delete();

        return response()->json([
            'message' => 'Module supprimé avec succès.'
        ]);
    }
}
