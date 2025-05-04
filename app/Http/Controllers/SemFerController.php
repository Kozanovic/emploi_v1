<?php

namespace App\Http\Controllers;

use App\Models\SemFer;
use Illuminate\Http\Request;

class SemFerController extends Controller
{
    public function index()
    {
        $semFers = SemFer::with(['ferie', 'semaine'])->get();

        return response()->json([
            'message' => 'Liste des jours fériés associés aux semaines récupérée avec succès',
            'data' => $semFers
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'ferie_id' => 'required|exists:feries,id',
            'semaine_id' => 'required|exists:semaines,id',
        ]);

        $semFer = SemFer::create($request->all());

        return response()->json([
            'message' => 'Association jour férié / semaine créée avec succès',
            'data' => $semFer->fresh(['ferie', 'semaine'])
        ]);
    }

    public function show($id)
    {
        $semFer = SemFer::with(['ferie', 'semaine'])->findOrFail($id);

        return response()->json([
            'message' => 'Association récupérée avec succès',
            'data' => $semFer
        ]);
    }

    public function update(Request $request, $id)
    {
        $semFer = SemFer::findOrFail($id);

        $request->validate([
            'ferie_id' => 'required|exists:feries,id',
            'semaine_id' => 'required|exists:semaines,id',
        ]);

        $semFer->update($request->all());

        return response()->json([
            'message' => 'Association mise à jour avec succès',
            'data' => $semFer->fresh(['ferie', 'semaine'])
        ]);
    }

    public function destroy($id)
    {
        $semFer = SemFer::findOrFail($id);

        $semFer->delete();

        return response()->json(['message' => 'Association supprimée avec succès']);
    }
}
