<?php

namespace App\Http\Controllers;

use App\Models\SectEfp;
use App\Models\Secteur;
use App\Models\Etablissement;
use Illuminate\Http\Request;

class SectEfpController extends Controller
{
    public function index()
    {
        $sectEfps = SectEfp::with(['secteur', 'etablissement'])->get();
        $secteurs = Secteur::all();
        $etablissements = Etablissement::all();
        return response()->json([
            'data' => $sectEfps,
            'secteurs' => $secteurs,
            'etablissements' => $etablissements,
            'message' => 'Liste des associations secteur/établissement récupérée avec succès',
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'secteur_id' => 'required|exists:secteurs,id',
            'etablissement_id' => 'required|exists:etablissements,id',
        ]);

        $sectEfp = SectEfp::create($request->all());

        return response()->json([
            'message' => 'Association créée avec succès',
            'data' => $sectEfp->fresh(['secteur', 'etablissement'])
        ]);
    }

    public function show($id)
    {
        $sectEfp = SectEfp::with(['secteur', 'etablissement'])->findOrFail($id);
        return response()->json([
            'message' => 'Association récupérée avec succès',
            'data' => $sectEfp
        ]);
    }

    public function update(Request $request, $id)
    {
        $sectEfp = SectEfp::findOrFail($id);
        $request->validate([
            'secteur_id' => 'required|exists:secteurs,id',
            'etablissement_id' => 'required|exists:etablissements,id',
        ]);

        $sectEfp->update($request->all());

        return response()->json([
            'message' => 'Association mise à jour avec succès',
            'data' => $sectEfp->fresh(['secteur', 'etablissement'])
        ]);
    }

    public function destroy($id)
    {
        $sectEfp = SectEfp::findOrFail($id);
        $sectEfp->delete();

        return response()->json(['message' => 'Association supprimée avec succès']);
    }
}
