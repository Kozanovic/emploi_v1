<?php

namespace App\Http\Controllers;

use App\Models\DirecteurRegional;
use Illuminate\Http\Request;

class DirecteurRegionalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $directeursRegionaux = DirecteurRegional::with('utilisateur')
            ->whereDoesntHave('directionRegional')
            ->get();
        return response()->json([
            'message' => 'Liste des directeurs régionaux récupérée avec succès.',
            'data' => $directeursRegionaux,
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

        // Création du directeur régional
        $directeurRegional = DirecteurRegional::create($validated);

        return response()->json([
            'message' => 'Directeur régional créé avec succès.',
            'data' => $directeurRegional,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $directeurRegional = DirecteurRegional::with('utilisateur')->findOrFail($id);

        return response()->json([
            'message' => 'Directeur régional récupéré avec succès.',
            'data' => $directeurRegional,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DirecteurRegional $directeurRegional)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $directeurRegional = DirecteurRegional::findOrFail($id);
        // Validation des champs nécessaires
        $validated = $request->validate([
            'utilisateur_id' => 'sometimes|required|exists:users,id',
        ]);

        // Mise à jour du directeur régional
        $directeurRegional->update($validated);

        return response()->json([
            'message' => 'Directeur régional mis à jour avec succès.',
            'data' => $directeurRegional,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $directeurRegional = DirecteurRegional::findOrFail($id);
        $directeurRegional->delete();

        return response()->json([
            'message' => 'Directeur régional supprimé avec succès.',
        ]);
    }
}
