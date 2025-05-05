<?php

namespace App\Http\Controllers;

use App\Models\Filiere;
use App\Models\Groupe;
use App\Models\Etablissement;
use Illuminate\Http\Request;

class GroupeController extends Controller
{
    public function index()
    {
        $groupes = Groupe::with(['filiere', 'etablissement'])->get();
        $filieres = Filiere::all();
        $etablissements = Etablissement::all();
        return response()->json([
            'data' => $groupes,
            'filieres' => $filieres,
            'etablissements' => $etablissements,
            'message' => 'Liste des groupes récupérée avec succès.'
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
            'data' => $groupe,
            'message' => 'Groupe créé avec succès.'
        ]);
    }

    public function show($id)
    {
        $groupe = Groupe::with(['filiere', 'etablissement'])->findOrFail($id);
        return response()->json([
            'data' => $groupe,
            'message' => 'Détails du groupe récupérés avec succès.'
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
            'data' => $groupe,
            'message' => 'Groupe mis à jour avec succès.'
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
