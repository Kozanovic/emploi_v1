<?php

namespace App\Http\Controllers;

use App\Models\Filiere;
use Illuminate\Http\Request;

class FiliereController extends Controller
{
    public function index()
    {
        //with(['modules', 'groupes', 'etablissements'])->get()
        $filieres = Filiere::all();
        return response()->json([
            'data' => $filieres,
            'message' => 'Liste des filières récupérée avec succès.'
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
        ]);

        $filiere = Filiere::create($validated);

        return response()->json([
            'data' => $filiere,
            'message' => 'Filière créée avec succès.'
        ]);
    }

    public function show($id)
    {
        //with(['modules', 'groupes', 'etablissements'])->
        $filiere = Filiere::findOrFail($id);

        return response()->json([
            'data' => $filiere,
            'message' => 'Détails de la filière récupérés avec succès.'
        ]);
    }

    public function update(Request $request, $id)
    {
        $filiere = Filiere::findOrFail($id);

        $validated = $request->validate([
            'nom' => 'sometimes|required|string|max:255',
        ]);

        $filiere->update($validated);
        $filiere->load(['modules', 'groupes', 'etablissements']);

        return response()->json([
            'data' => $filiere,
            'message' => 'Filière mise à jour avec succès.'
        ]);
    }

    public function destroy($id)
    {
        $filiere = Filiere::findOrFail($id);
        $filiere->delete();

        return response()->json([
            'message' => 'Filière supprimée avec succès.'
        ]);
    }
}
