<?php

namespace App\Http\Controllers;

use App\Models\Ferie;
use Illuminate\Http\Request;

class FerieController extends Controller
{
    public function index()
    {
        $feries = Ferie::all();
        return response()->json([
            'data' => $feries,
            'message' => 'Liste des jours fériés récupérée avec succès.'
        ], 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
        ]);

        $ferie = Ferie::create($validated);

        return response()->json([
            'message' => 'Jour férié créé avec succès.',
            'data' => $ferie
        ], 201);
    }

    public function show($id)
    {
        $ferie = Ferie::findOrFail($id);
        return response()->json([
            'data' => $ferie,
            'message' => 'Jour férié récupéré avec succès.'
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $ferie = Ferie::findOrFail($id);
        $validated = $request->validate([
            'nom' => 'sometimes|required|string',
            'date_debut' => 'sometimes|required|date',
            'date_fin' => 'sometimes|required|date|after_or_equal:date_debut',
        ]);

        $ferie->update($validated);

        return response()->json([
            'message' => 'Jour férié mis à jour.',
            'data' => $ferie
        ], 200);
    }

    public function destroy($id)
    {
        $ferie = Ferie::findOrFail($id);
        $ferie->delete();

        return response()->json([
            'message' => 'Jour férié supprimé.'
        ], 200);
    }
}
