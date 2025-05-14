<?php

namespace App\Http\Controllers;

use App\Models\Complexe;
use Illuminate\Http\Request;

class ComplexeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $complexes = Complexe::with('directionRegional')->get();
        return response()->json([
            'message' => 'Liste des complexes récupérée avec succès.',
            'data' => $complexes,
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
        $request->validate([
            'nom' => 'required|string|max:255',
            'direction_regional_id' => 'required|exists:direction_regionals,id'
        ]);
        $complexe = Complexe::create($request->all());

        return response()->json([
            'message' => 'Complexe créé avec succès.',
            'data' => $complexe
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $complexe = Complexe::findOrFail($id);
        $complexe->load('directionRegional');
        return response()->json([
            'message' => 'Complexe récupéré avec succès.',
            'data' => $complexe
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Complexe $complexe)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Complexe $complexe)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'direction_regional_id' => 'required|exists:direction_regionals,id'
        ]);

        $complexe->update($request->all());

        return response()->json([
            'message' => 'Complexe mis à jour avec succès.',
            'data' => $complexe->fresh(['directionRegional'])
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Complexe $complexe)
    {
        $complexe->delete();

        return response()->json([
            'message' => 'Complexe supprimé avec succès.'
        ]);
    }
}
