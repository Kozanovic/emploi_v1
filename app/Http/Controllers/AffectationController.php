<?php

namespace App\Http\Controllers;

use App\Models\Affectation;
use Illuminate\Http\Request;

class AffectationController extends Controller
{
    public function index()
    {
        $affectations = Affectation::with(['formateur', 'module', 'groupe'])->get();
        return response()->json([
            'message' => 'Liste des affectations récupérée avec succès',
            'data' => $affectations
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'formateur_id' => 'required|exists:formateurs,id',
            'module_id' => 'required|exists:modules,id',
            'groupe_id' => 'required|exists:groupes,id',
        ]);

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
}
