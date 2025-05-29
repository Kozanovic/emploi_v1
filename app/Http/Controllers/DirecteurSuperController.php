<?php

namespace App\Http\Controllers;

use App\Models\DirecteurSuper;
use Illuminate\Http\Request;

class DirecteurSuperController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $directeurSupers = DirecteurSuper::with('utilisateur')->get();
        return response()->json([
            'message' => 'Liste des directeurs supérieurs récupérée avec succès.',
            'data' => $directeurSupers,
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

        // Création du directeur supérieur
        $directeurSuper = DirecteurSuper::create($validated);

        return response()->json([
            'message' => 'Directeur supérieur créé avec succès.',
            'data' => $directeurSuper,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $directeurSuper = DirecteurSuper::findOrFail($id);
        return response()->json([
            'message' => 'Directeur supérieur récupéré avec succès.',
            'data' => $directeurSuper,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DirecteurSuper $directeurSuper)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $directeurSuper = DirecteurSuper::findOrFail($id);
        // Validation des champs nécessaires
        $validated = $request->validate([
            'utilisateur_id' => 'sometimes|required|exists:users,id',
        ]);

        // Mise à jour du directeur supérieur
        $directeurSuper->update($validated);

        return response()->json([
            'message' => 'Directeur supérieur mis à jour avec succès.',
            'data' => $directeurSuper,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $directeurSuper = DirecteurSuper::findOrFail($id);
        $directeurSuper->delete();

        return response()->json([
            'message' => 'Directeur supérieur supprimé avec succès.',
        ]);
    }
}
