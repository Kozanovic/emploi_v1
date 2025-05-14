<?php

namespace App\Http\Controllers;

use App\Models\DirectionRegional;
use Illuminate\Http\Request;

class DirectionRegionalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $directionRegionals = DirectionRegional::all();
        return response()->json([
            'message' => 'Liste des directions régionales récupérée avec succès',
            'data' => $directionRegionals
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
            'adresse' => 'required|string|max:255',
            'telephone' => 'required|string|max:255',
        ]);

        $directionRegional = DirectionRegional::create($request->all());
        return response()->json([
            'message' => 'Direction régionale créée avec succès',
            'data' => $directionRegional
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $directionRegional = DirectionRegional::findOrFail($id);
        return response()->json([
            'message' => 'Direction régionale récupérée avec succès',
            'data' => $directionRegional
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DirectionRegional $directionRegional)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DirectionRegional $directionRegional)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'adresse' => 'required|string|max:255',
            'telephone' => 'required|string|max:255',
        ]);

        $directionRegional->update($request->all());

        return response()->json([
            'message' => 'Direction régionale mise à jour avec succès',
            'data' => $directionRegional
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DirectionRegional $directionRegional)
    {
        $directionRegional->delete();

        return response()->json([
            'message' => 'Direction régionale supprimée avec succès'
        ]);
    }
}
