<?php

namespace App\Http\Controllers;

use App\Models\Module;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    public function index()
    {
        $modules = Module::with(['filiere'])->get();
        return response()->json([
            'message' => 'Liste des modules récupérée avec succès.',
            'data' => $modules,
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
            'annee_formation' => 'required',
            'filiere_id' => 'required|exists:filieres,id',
        ]);

        $module = Module::create($validated)->fresh(['filiere']);

        return response()->json([
            'message' => 'Module créé avec succès.',
            'data' => $module,
        ]);
    }

    public function show($id)
    {
        $module = Module::with(['filiere'])->findOrFail($id);
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
            'annee_formation' => 'sometimes|required',
            'filiere_id' => 'sometimes|required|exists:filieres,id',
        ]);

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
        $module->delete();

        return response()->json([
            'message' => 'Module supprimé avec succès.'
        ]);
    }
}
