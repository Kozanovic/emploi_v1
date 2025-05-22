<?php

namespace App\Http\Controllers;

use App\Models\AnneeScolaire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

class AnneeScolaireController extends Controller
{
    public function index()
    {
        $annees = AnneeScolaire::all();
        // Vérifier si l'utilisateur a le droit de voir la liste des années scolaires
        $currentUser = Auth::user();
        if (!Gate::forUser($currentUser)->allows('view', AnneeScolaire::class)) {
            return response()->json([
                'message' => "Vous n'avez pas le droit de voir la liste des années scolaires.",
            ], 403);
        }
        return response()->json([
            'message' => 'Liste des années scolaires récupérée avec succès.',
            'data' => $annees,
        ], 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after:date_debut',
        ]);
        // Vérifier si l'utilisateur a le droit de créer une année scolaire
        $currentUser = Auth::user();
        if (!Gate::forUser($currentUser)->allows('create', AnneeScolaire::class)) {
            return response()->json([
                'message' => "Vous n'avez pas le droit de créer une année scolaire.",
            ], 403);
        }
        // Vérifier si l'année scolaire existe déjà
        $existingAnnee = AnneeScolaire::where('nom', $validated['nom'])
            ->where('date_debut', $validated['date_debut'])
            ->where('date_fin', $validated['date_fin'])
            ->first();
        if ($existingAnnee) {
            return response()->json([
                'message' => 'Cette année scolaire existe déjà.',
            ], 409);
        }
        // Créer une nouvelle année scolaire

        $annee = AnneeScolaire::create($validated);

        return response()->json([
            'message' => 'Année scolaire créée avec succès.',
            'data' => $annee
        ], 201);
    }

    public function show($id)
    {
        $annee = AnneeScolaire::findOrFail($id);
        // Vérifier si l'utilisateur a le droit de voir une année scolaire
        $currentUser = Auth::user();
        if (!Gate::forUser($currentUser)->allows('view', $annee)) {
            return response()->json([
                'message' => "Vous n'avez pas le droit de voir cette année scolaire.",
            ], 403);
        }
        return response()->json([
            'message' => 'Année scolaire récupérée avec succès.',
            'data' => $annee,
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $annee = AnneeScolaire::findOrFail($id);
        $validated = $request->validate([
            'nom' => 'sometimes|required|string',
            'date_debut' => 'sometimes|required|date',
            'date_fin' => 'sometimes|required|date|after:date_debut',
        ]);

        // Vérifier si l'utilisateur a le droit de mettre à jour une année scolaire
        $currentUser = Auth::user();
        if (!Gate::forUser($currentUser)->allows('update', $annee)) {
            return response()->json([
                'message' => "Vous n'avez pas le droit de mettre à jour cette année scolaire.",
            ], 403);
        }

        $annee->update($validated);
        return response()->json([
            'message' => 'Année scolaire mise à jour.',
            'data' => $annee,
        ], 200);
    }

    public function destroy($id)
    {
        $annee = AnneeScolaire::findOrFail($id);
        // Vérifier si l'utilisateur a le droit de supprimer une année scolaire
        $currentUser = Auth::user();
        if (!Gate::forUser($currentUser)->allows('delete', $annee)) {
            return response()->json([
                'message' => "Vous n'avez pas le droit de supprimer cette année scolaire.",
            ], 403);
        }
        $annee->delete();

        return response()->json([
            'message' => 'Année scolaire supprimée.'
        ], 200);
    }
}
