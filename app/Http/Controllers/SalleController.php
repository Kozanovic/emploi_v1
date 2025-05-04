<?php

namespace App\Http\Controllers;

use App\Models\Salle;
use Illuminate\Http\Request;

class SalleController extends Controller
{
    public function index()
    {
        $salles = Salle::with(['etablissement'])->get();
        return response()->json([
            'data' => $salles,
            'message' => 'Liste des salles récupérée avec succès.'
        ], 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string',
            'capacite' => 'required|integer|min:1',
            'type' => 'required|in:Salle,Atelier',
            'etablissement_id' => 'required|exists:etablissements,id',
        ]);

        $salle = Salle::create($validated);

        return response()->json([
            'message' => 'Salle créée avec succès.',
            'data' => $salle
        ], 201);
    }

    public function show($id)
    {
        $salle = Salle::with('etablissement')->findOrFail($id);
        return response()->json([
            'data' => $salle,
            'message' => 'Salle récupérée avec succès.'
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $salle = Salle::findOrFail($id);

        $validated = $request->validate([
            'nom' => 'sometimes|required|string',
            'capacite' => 'sometimes|required|integer|min:1',
            'type' => 'sometimes|required|in:Salle,Atelier',
            'etablissement_id' => 'sometimes|required|exists:etablissements,id',
        ]);

        $salle->update($validated);

        return response()->json([
            'message' => 'Salle mise à jour.',
            'data' => $salle->fresh(['etablissement'])
        ], 200);
    }

    public function destroy($id)
    {
        $salle = Salle::findOrFail($id);
        $salle->delete();

        return response()->json([
            'message' => 'Salle supprimée.'
        ], 200);
    }
}
