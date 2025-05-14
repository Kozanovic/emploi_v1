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
            'message' => 'Liste des filières récupérée avec succès.',
            'data' => $filieres,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
        ]);

        $filiere = Filiere::create($validated);

        return response()->json([
            'message' => 'Filière créée avec succès.',
            'data' => $filiere,
        ]);
    }

    public function show($id)
    {
        //with(['modules', 'groupes', 'etablissements'])->
        $filiere = Filiere::findOrFail($id);

        return response()->json([
            'message' => 'Détails de la filière récupérés avec succès.',
            'data' => $filiere,
        ]);
    }

    public function update(Request $request, $id)
    {
        $filiere = Filiere::findOrFail($id);

        $validated = $request->validate([
            'nom' => 'sometimes|required|string|max:255',
        ]);

        $filiere->update($validated);

        return response()->json([
            'message' => 'Filière mise à jour avec succès.',
            'data' => $filiere,
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
