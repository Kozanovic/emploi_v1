<?php

namespace App\Http\Controllers;

use App\Models\Ferie;
use App\Models\Seance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class FerieController extends Controller
{
    public function index()
    {
        $feries = Ferie::all();
        // Vérifier si l'utilisateur a le droit de voir la liste des jours fériés
        $currentUser = Auth::user();
        if (!Gate::forUser($currentUser)->allows('view', Ferie::class)) {
            return response()->json([
                'message' => "Vous n'avez pas le droit de voir la liste des jours fériés.",
            ], 403);
        }
        return response()->json([
            'message' => 'Liste des jours fériés récupérée avec succès.',
            'data' => $feries,
        ], 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
        ]);
        // Vérifier si l'utilisateur a le droit de créer un jour férié
        $currentUser = Auth::user();
        if (!Gate::forUser($currentUser)->allows('create', Ferie::class)) {
            return response()->json([
                'message' => "Vous n'avez pas le droit de créer un jour férié.",
            ], 403);
        }

        $ferie = Ferie::create($validated);

        Seance::where('date_seance', '>=', $validated['date_debut'])
            ->where('date_seance', '<', $validated['date_fin'])
            ->update(['supprime_par_ferie_id' => $ferie->id]);

        return response()->json([
            'message' => 'Jour férié créé avec succès.',
            'data' => $ferie
        ], 201);
    }

    public function show($id)
    {
        $ferie = Ferie::findOrFail($id);
        // Vérifier si l'utilisateur a le droit de voir un jour férié
        $currentUser = Auth::user();
        if (!Gate::forUser($currentUser)->allows('view', $ferie)) {
            return response()->json([
                'message' => "Vous n'avez pas le droit de voir ce jour férié.",
            ], 403);
        }
        return response()->json([
            'message' => 'Jour férié récupéré avec succès.',
            'data' => $ferie,
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $ferie = Ferie::findOrFail($id);
        // Vérifier si l'utilisateur a le droit de mettre à jour un jour férié
        $currentUser = Auth::user();
        if (!Gate::forUser($currentUser)->allows('update', $ferie)) {
            return response()->json([
                'message' => "Vous n'avez pas le droit de mettre à jour ce jour férié.",
            ], 403);
        }
        $validated = $request->validate([
            'nom' => 'sometimes|required|string',
            'date_debut' => 'sometimes|required|date',
            'date_fin' => 'sometimes|required|date|after_or_equal:date_debut',
        ]);

        $ferie->update($validated);

        $newStart = $ferie->date_debut;
        $newEnd = $ferie->date_fin;

        Seance::where('supprime_par_ferie_id', $ferie->id)
            ->where(function ($query) use ($newStart, $newEnd) {
                $query->where('date_seance', '<', $newStart)
                    ->orWhere('date_seance', '>', $newEnd);
            })
            ->update(['supprime_par_ferie_id' => null]);

        Seance::where('date_seance', '>=', $validated['date_debut'])
            ->where('date_seance', '<', $validated['date_fin'])
            ->update(['supprime_par_ferie_id' => $ferie->id]);

        return response()->json([
            'message' => 'Jour férié mis à jour.',
            'data' => $ferie
        ], 200);
    }

    public function destroy($id)
    {
        $ferie = Ferie::findOrFail($id);
        // Vérifier si l'utilisateur a le droit de supprimer un jour férié
        $currentUser = Auth::user();
        if (!Gate::forUser($currentUser)->allows('delete', $ferie)) {
            return response()->json([
                'message' => "Vous n'avez pas le droit de supprimer ce jour férié.",
            ], 403);
        }
        Seance::where('supprime_par_ferie_id', $ferie->id)
            ->update(['supprime_par_ferie_id' => null]);
        $ferie->delete();

        return response()->json([
            'message' => 'Jour férié supprimé.'
        ], 200);
    }
}
