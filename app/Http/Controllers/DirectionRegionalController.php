<?php

namespace App\Http\Controllers;

use App\Models\DirectionRegional;
use App\Models\DirecteurRegional;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class DirectionRegionalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        // Vérification des autorisations
        if (!Gate::allows('view', DirectionRegional::class)) {
            return response()->json(['message' => 'Non autorisé à voir la liste des directions régionales.'], 403);
        }
        // Récupération de toutes les directions régionales
        $directionRegionals = DirectionRegional::with('directeurRegional.utilisateur')->get();

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
            'directeur_regional_id' => 'required|exists:direction_regionals,id'
        ]);

        // Vérification des autorisations
        if (!Gate::allows('create', DirectionRegional::class)) {
            return response()->json(['message' => 'Non autorisé à créer une direction régionale.'], 403);
        }
        // Création de la direction régionale

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
        // Vérification des autorisations
        if (!Gate::allows('view', DirectionRegional::class)) {
            return response()->json(['message' => 'Non autorisé à voir la direction régionale.'], 403);
        }
        // Récupération de la direction régionale par son ID
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
    public function update(Request $request, $id)
    {
        // Vérification des autorisations
        $directionRegional = DirectionRegional::findOrFail($id);
        if (!Gate::allows('update', $directionRegional)) {
            return response()->json(['message' => 'Non autorisé à mettre à jour la direction régionale.'], 403);
        }
        $request->validate([
            'nom' => 'required|string|max:255',
            'adresse' => 'required|string|max:255',
            'telephone' => 'required|string|max:255',
            'directeur_regional_id' => 'sometimes|required|exists:direction_regionals,id'
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
    public function destroy($id)
    {
        // Vérification des autorisations
        $directionRegional = DirectionRegional::findOrFail($id);
        if (!Gate::allows('delete', $directionRegional)) {
            return response()->json(['message' => 'Non autorisé à supprimer la direction régionale.'], 403);
        }
        $directionRegional->delete();

        return response()->json([
            'message' => 'Direction régionale supprimée avec succès'
        ]);
    }
}
