<?php

namespace App\Http\Controllers;

use App\Models\AnneeScolaire;
use App\Models\Semaine;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class SemaineController extends Controller
{
    public function index()
    {
        $semaines = Semaine::with(['anneeScolaire'])->get();
        $annees = AnneeScolaire::all();
        // Vérifier si l'utilisateur a le droit de voir la liste des modules
        $currentUser = Auth::user();
        if (!Gate::forUser($currentUser)->allows('view', Semaine::class)) {
            return response()->json([
                'message' => "Vous n'avez pas le droit de voir la liste des semaines.",
            ], 403);
        }
        return response()->json([
            'message' => 'Liste des semaines récupérée avec succès.',
            'data' => $semaines,
            'annees' => $annees,
        ], 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'numero_semaine' => 'required|integer|min:1',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'annee_scolaire_id' => 'required|exists:annee_scolaires,id',
        ]);
        // Vérifier si l'utilisateur a le droit de créer un module
        $currentUser = Auth::user();
        if (!Gate::forUser($currentUser)->allows('create', Semaine::class)) {
            return response()->json([
                'message' => "Vous n'avez pas le droit de créer une semaine.",
            ], 403);
        }

        $semaine = Semaine::create($validated);

        return response()->json([
            'message' => 'Semaine créée avec succès.',
            'data' => $semaine,
        ], 201);
    }

    public function show($id)
    {
        $semaine = Semaine::with(['anneeScolaire'])->findOrFail($id);
        // Vérifier si l'utilisateur a le droit de voir les détails d'un module
        $currentUser = Auth::user();
        if (!Gate::forUser($currentUser)->allows('viewAny', $semaine)) {
            return response()->json([
                'message' => "Vous n'avez pas le droit de voir les détails de cette semaine.",
            ], 403);
        }

        return response()->json([
            'message' => 'Semaine récupérée avec succès.',
            'data' => $semaine,
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $semaine = Semaine::findOrFail($id);

        $validated = $request->validate([
            'numero_semaine' => 'sometimes|required|integer|min:1',
            'date_debut' => 'sometimes|required|date',
            'date_fin' => 'sometimes|required|date|after_or_equal:date_debut',
            'annee_scolaire_id' => 'sometimes|required|exists:annee_scolaires,id',
        ]);
        // Vérifier si l'utilisateur a le droit de mettre à jour un module
        $currentUser = Auth::user();
        if (!Gate::forUser($currentUser)->allows('update', Semaine::class)) {
            return response()->json([
                'message' => "Vous n'avez pas le droit de mettre à jour cette semaine.",
            ], 403);
        }

        $semaine->update($validated);

        return response()->json([
            'message' => 'Semaine mise à jour.',
            'data' => $semaine->fresh(['anneeScolaire'])
        ], 200);
    }

    public function destroy($id)
    {
        $semaine = Semaine::findOrFail($id);
        // Vérifier si l'utilisateur a le droit de supprimer un module
        $currentUser = Auth::user();
        if (!Gate::forUser($currentUser)->allows('delete', $semaine)) {
            return response()->json([
                'message' => "Vous n'avez pas le droit de supprimer cette semaine.",
            ], 403);
        }
        $semaine->delete();

        return response()->json([
            'message' => 'Semaine supprimée.'
        ], 200);
    }
}
