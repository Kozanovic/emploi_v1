<?php

namespace App\Http\Controllers;

use App\Models\AnneeScolaire;
use Illuminate\Http\Request;

class AnneeScolaireController extends Controller
{
    public function index()
    {
        $annees = AnneeScolaire::all();
        return response()->json([
            'message' => 'Liste des années scolaires récupérée avec succès.',
            'data' => $annees,
        ], 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after:date_debut',
        ]);

        $annee = AnneeScolaire::create($validated);

        return response()->json([
            'message' => 'Année scolaire créée avec succès.',
            'data' => $annee
        ], 201);
    }

    public function show($id)
    {
        $annee = AnneeScolaire::findOrFail($id);
        return response()->json([
            'message' => 'Année scolaire récupérée avec succès.',
            'data' => $annee,
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $annee = AnneeScolaire::findOrFail($id);
        $validated = $request->validate([
            'nom' => 'sometimes|required|string',
            'date_debut' => 'sometimes|required|date',
            'date_fin' => 'sometimes|required|date|after:date_debut',
        ]);

        $annee->update($validated);

        return response()->json([
            'message' => 'Année scolaire mise à jour.',
            'data' => $annee,
        ], 200);
    }

    public function destroy($id)
    {
        $annee = AnneeScolaire::findOrFail($id);
        $annee->delete();

        return response()->json([
            'message' => 'Année scolaire supprimée.'
        ], 200);
    }
}
