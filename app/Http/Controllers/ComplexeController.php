<?php

namespace App\Http\Controllers;

use App\Models\Complexe;
use App\Models\DirecteurComplexe;
use App\Models\DirectionRegional;
use App\Models\DirecteurRegional;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

class ComplexeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        if (!Gate::allows('view', Complexe::class)) {
            return response()->json(['message' => 'Non autorisé à voir la liste des complexes.'], 403);
        }

        $directeurRegional = DirecteurRegional::where('utilisateur_id', $user->id)->first();

        if (!$directeurRegional || !$directeurRegional->directionRegional) {
            return response()->json(['message' => 'Direction régionale introuvable.'], 404);
        }

        $directionRegional = $directeurRegional->directionRegional;

        // Récupérer les complexes de cette direction régionale
        $complexes = Complexe::with('directionRegional')
            ->where('direction_regional_id', $directionRegional->id)
            ->get();

        // Extraire les IDs des directeur_complexes liés à ces complexes
        $directeurComplexeIds = $complexes->pluck('directeur_complexe_id')->unique();

        // Récupérer uniquement ces directeurComplexes avec leur utilisateur
        $directeurComplexes = DirecteurComplexe::with('utilisateur')
            ->whereIn('id', $directeurComplexeIds)
            ->get();

        $directionRegionales = DirectionRegional::all();

        return response()->json([
            'message' => 'Liste des complexes récupérée avec succès.',
            'data' => $complexes,
            'direction_regionales' => $directionRegionales,
            'directeur_complexes' => $directeurComplexes,
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
            'direction_regional_id' => 'required|exists:direction_regionals,id',
            'directeur_complexe_id' => 'required|exists:directeur_complexes,id',
        ]);
        // Vérification des autorisations
        if (!Gate::allows('create', Complexe::class)) {
            return response()->json(['message' => 'Non autorisé à créer un complexe.'], 403);
        }
        // Création du complexe
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
        // Vérification des autorisations
        if (!Gate::allows('view', Complexe::class)) {
            return response()->json(['message' => 'Non autorisé à voir le complexe.'], 403);
        }
        // Récupération du complexe
        // Vérification de l'existence du complexe
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
    public function update(Request $request, $id)
    {
        $complexe = Complexe::findOrFail($id);
        $request->validate([
            'nom' => 'required|string|max:255',
            'directeur_regional_id' => 'required|exists:directeur_regionals,id',
            'directeur_complexe_id' => 'required|exists:directeur_complexes,id',
        ]);
        // Vérification des autorisations
        if (!Gate::allows('update', $complexe)) {
            return response()->json(['message' => 'Non autorisé à mettre à jour le complexe.'], 403);
        }

        $complexe->update($request->all());

        return response()->json([
            'message' => 'Complexe mis à jour avec succès.',
            'data' => $complexe->fresh(['directionRegional'])
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $complexe = Complexe::findOrFail($id);
        // Vérification des autorisations
        if (!Gate::allows('delete', $complexe)) {
            return response()->json(['message' => 'Non autorisé à supprimer le complexe.'], 403);
        }
        $complexe->delete();

        return response()->json([
            'message' => 'Complexe supprimé avec succès.'
        ]);
    }
}
