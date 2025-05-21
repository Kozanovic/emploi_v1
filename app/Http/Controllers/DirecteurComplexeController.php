<?php

namespace App\Http\Controllers;

use App\Models\DirecteurComplexe;
use Illuminate\Http\Request;

class DirecteurComplexeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $directeurComplexes = DirecteurComplexe::with('utilisateur')->get();
        return response()->json([
            'message' => 'liste des directeurs complexes récupérée avec succès.',
            'data' => $directeurComplexes,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validation des champs nécessaires
        $validated = $request->validate([
            'utilisateur_id' => 'required|exists:users,id',
        ]);

        // Création du directeur complexe
        $directeurComplexe = DirecteurComplexe::create($validated);

        return response()->json([
            'message' => 'Directeur complexe créé avec succès.',
            'data' => $directeurComplexe,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $directeurComplexe = DirecteurComplexe::with('utilisateur')->findOrFail($id);
        return response()->json([
            'message' => 'Directeur complexe récupéré avec succès.',
            'data' => $directeurComplexe,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DirecteurComplexe $directeurComplexe)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Validation des champs nécessaires
        $validated = $request->validate([
            'utilisateur_id' => 'sometimes|required|exists:users,id',
        ]);

        // Mise à jour du directeur complexe
        $directeurComplexe = DirecteurComplexe::findOrFail($id);
        $directeurComplexe->update($validated);

        return response()->json([
            'message' => 'Directeur complexe mis à jour avec succès.',
            'data' => $directeurComplexe,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $directeurComplexe = DirecteurComplexe::findOrFail($id);
        $directeurComplexe->delete();

        return response()->json([
            'message' => 'Directeur complexe supprimé avec succès.',
        ]);
    }
}
