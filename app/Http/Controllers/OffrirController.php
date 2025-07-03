<?php

namespace App\Http\Controllers;

use App\Models\Offrir;
use Illuminate\Http\Request;
use App\Models\Etablissement;
use App\Models\Filiere;
use Illuminate\Support\Facades\Auth;

class OffrirController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $directeurId = $user->directeurEtablissement->etablissement->id;
        $offrirs = Offrir::with(['filiere', 'etablissement'])
            ->where('etablissement_id', $directeurId)
            ->get();
        $filieres = Filiere::whereDoesntHave('etablissements')->with('secteur')->get();
        return response()->json([
            'message' => 'Liste des associations filière/établissement récupérée avec succès',
            'data' => $offrirs,
            'filieres' => $filieres,
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $etablissementId = $user->directeurEtablissement->etablissement->id;
        $validated = $request->validate([
            'filiere_id' => 'required|exists:filieres,id',
        ]);

        $offrir = Offrir::create(array_merge($validated, [
            'etablissement_id' => $etablissementId,
        ]));

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
        $user = Auth::user();
        $etablissementId = $user->directeurEtablissement->etablissement->id;
        $valiated = $request->validate([
            'filiere_id' => 'required|exists:filieres,id',
            'etablissement_id' => 'required|exists:etablissements,id',
        ]);

        $offrir->update(array_merge($valiated, [
            'etablissement_id' => $etablissementId,
        ]));

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
