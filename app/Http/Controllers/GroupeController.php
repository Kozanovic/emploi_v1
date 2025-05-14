<?php

namespace App\Http\Controllers;

use App\Models\Groupe;
use Illuminate\Http\Request;

class GroupeController extends Controller
{
    public function index()
    {
        $groupes = Groupe::with(['filiere', 'etablissement'])->get();
        return response()->json([
            'message' => 'Liste des groupes récupérée avec succès.',
            'data' => $groupes,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string',
            'annee' => 'required|integer',
            'filiere_id' => 'required|exists:filieres,id',
            'etablissement_id' => 'required|exists:etablissements,id',
        ]);

        $groupe = Groupe::create($validated)->fresh(['filiere', 'etablissement']);

        return response()->json([
            'message' => 'Groupe créé avec succès.',
            'data' => $groupe,
        ]);
    }

    public function show($id)
    {
        $groupe = Groupe::with(['filiere', 'etablissement'])->findOrFail($id);
        return response()->json([
            'message' => 'Détails du groupe récupérés avec succès.',
            'data' => $groupe,
        ]);
    }

    public function update(Request $request, $id)
    {
        $groupe = Groupe::findOrFail($id);

        $validated = $request->validate([
            'nom' => 'sometimes|required|string',
            'annee' => 'sometimes|required|integer',
            'filiere_id' => 'sometimes|required|exists:filieres,id',
            'etablissement_id' => 'sometimes|required|exists:etablissements,id',
        ]);

        $groupe->update($validated);
        $groupe->load(['filiere', 'etablissement']);

        return response()->json([
            'message' => 'Groupe mis à jour avec succès.',
            'data' => $groupe,
        ]);
    }

    public function destroy($id)
    {
        $groupe = Groupe::findOrFail($id);
        $groupe->delete();

        return response()->json([
            'message' => 'Groupe supprimé avec succès.'
        ]);
    }
}
