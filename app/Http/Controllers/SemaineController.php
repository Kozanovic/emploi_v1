<?php

namespace App\Http\Controllers;

use App\Models\Semaine;
use Illuminate\Http\Request;

class SemaineController extends Controller
{
    public function index()
    {
        $semaines = Semaine::with(['anneeScolaire'])->get();
        return response()->json([
            'data' => $semaines,
            'message' => 'Liste des semaines récupérée avec succès.'
        ], 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'numero_semaine' => 'required|integer|min:1',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'annee_scolaire_id' => 'required|exists:annee_scolaires,id',
        ]);

        $semaine = Semaine::create($validated);

        return response()->json([
            'data' => $semaine,
            'message' => 'Semaine créée avec succès.'
        ], 201);
    }

    public function show($id)
    {
        $semaine = Semaine::with(['anneeScolaire'])->findOrFail($id);
        
        return response()->json([
            'data' => $semaine,
            'message' => 'Semaine récupérée avec succès.'
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $semaine = Semaine::findOrFail($id);

        $validated = $request->validate([
            'numero_semaine' => 'sometimes|required|integer|min:1',
            'date_debut' => 'sometimes|required|date',
            'date_fin' => 'sometimes|required|date|after_or_equal:date_debut',
            'annee_scolaire_id' => 'sometimes|required|exists:annee_scolaires,id',
        ]);

        $semaine->update($validated);

        return response()->json([
            'message' => 'Semaine mise à jour.',
            'data' => $semaine->fresh(['anneeScolaire'])
        ], 200);
    }

    public function destroy($id)
    {
        $semaine = Semaine::findOrFail($id);
        $semaine->delete();

        return response()->json([
            'message' => 'Semaine supprimée.'
        ], 200);
    }
}
