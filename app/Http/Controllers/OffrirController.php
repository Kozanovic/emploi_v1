<?php

namespace App\Http\Controllers;

use App\Models\Offrir;
use Illuminate\Http\Request;

class OffrirController extends Controller
{
    public function index()
    {
        $offrirs = Offrir::with(['filiere', 'etablissement'])->get();
        return response()->json([
            'message' => 'Liste des associations filière/établissement récupérée avec succès',
            'data' => $offrirs
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'filiere_id' => 'required|exists:filieres,id',
            'etablissement_id' => 'required|exists:etablissements,id',
        ]);

        $offrir = Offrir::create($request->all());

        return response()->json([
            'message' => 'Association créée avec succès',
            'data' => $offrir->fresh(['filiere', 'etablissement'])
        ]);
    }

    public function show($id)
    {
        $offrir = Offrir::with(['filiere', 'etablissement'])->findOrFail($id);
        return response()->json([
            'message' => 'Association récupérée avec succès',
            'data' => $offrir
        ]);
    }

    public function update(Request $request, $id)
    {
        $offrir = Offrir::findOrFail($id);

        $request->validate([
            'filiere_id' => 'required|exists:filieres,id',
            'etablissement_id' => 'required|exists:etablissements,id',
        ]);

        $offrir->update($request->all());

        return response()->json([
            'message' => 'Association mise à jour avec succès',
            'data' => $offrir->fresh(['filiere', 'etablissement'])
        ]);
    }

    public function destroy($id)
    {
        $offrir = Offrir::findOrFail($id);

        $offrir->delete();

        return response()->json(['message' => 'Association supprimée avec succès']);
    }
}
