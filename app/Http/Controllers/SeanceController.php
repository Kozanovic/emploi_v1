<?php

namespace App\Http\Controllers;

use App\Models\Seance;
use App\Models\Semaine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class SeanceController extends Controller
{
    public function index()
    {
        // Vérifier si l'utilisateur a le droit de voir la liste des séances
        $currentUser = Auth::user();
        if (!Gate::forUser($currentUser)->allows('view', Seance::class)) {
            return response()->json([
                'message' => "Vous n'avez pas le droit de voir la liste des séances.",
            ], 403);
        }
        $etablissement = $currentUser->directeurEtablissement->etablissement;
        $semaine = Semaine::with('anneeScolaire', 'etablissement')
            ->where('etablissement_id', $etablissement->id)
            ->orderBy('date_fin', 'desc')
            ->limit(1)
            ->get();
        $seances = Seance::with(['semaine', 'salle', 'module', 'formateur', 'groupe'])->whereHas('semaine', function ($query) use ($etablissement) {
                $query->where('etablissement_id', $etablissement->id);
            })
            ->get();
        return response()->json([
            'message' => 'Liste des séances récupérée avec succès.',
            'data' => $seances,
            'semaine' => $semaine,
        ], 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date_seance' => 'required|date',
            'heure_debut' => 'required|date_format:H:i',
            'heure_fin' => 'required|date_format:H:i|after:heure_debut',
            'type' => 'required|in:presentiel,distanciel',
            'semaine_id' => 'required|exists:semaines,id',
            'salle_id' => 'required|exists:salles,id',
            'module_id' => 'required|exists:modules,id',
            'formateur_id' => 'required|exists:formateurs,id',
            'groupe_id' => 'required|exists:groupes,id',
        ]);

        // Vérifier si l'utilisateur a le droit de créer une séance
        $currentUser = Auth::user();
        if (!Gate::forUser($currentUser)->allows('create', Seance::class)) {
            return response()->json([
                'message' => "Vous n'avez pas le droit de créer une séance.",
            ], 403);
        }
        $seance = Seance::create($validated);

        return response()->json([
            'message' => 'Séance créée avec succès.',
            'data' => $seance
        ], 201);
    }

    public function show($id)
    {
        $seance = Seance::with(['semaine', 'salle', 'module', 'formateur', 'groupe'])->findOrFail($id);
        // Vérifier si l'utilisateur a le droit de voir une séance
        $currentUser = Auth::user();
        if (!Gate::forUser($currentUser)->allows('viewAny', $seance)) {
            return response()->json([
                'message' => "Vous n'avez pas le droit de voir cette séance.",
            ], 403);
        }
        return response()->json([
            'message' => 'Séance récupérée avec succès.',
            'data' => $seance,
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $seance = Seance::findOrFail($id);

        $validated = $request->validate([
            'date_seance' => 'sometimes|required|date',
            'heure_debut' => 'sometimes|required',
            'heure_fin' => 'sometimes|required|after:heure_debut',
            'type' => 'sometimes|required|in:presentiel,distanciel',
            'semaine_id' => 'sometimes|required|exists:semaines,id',
            'salle_id' => 'sometimes|required|exists:salles,id',
            'module_id' => 'sometimes|required|exists:modules,id',
            'formateur_id' => 'sometimes|required|exists:formateurs,id',
            'groupe_id' => 'sometimes|required|exists:groupes,id',
        ]);

        // Vérification des permissions
        $currentUser = Auth::user();
        if (!Gate::forUser($currentUser)->allows('update', $seance)) {
            return response()->json([
                'message' => "Vous n'avez pas le droit de mettre à jour cette séance.",
            ], 403);
        }

        try {
            $seance->update($validated);

            return response()->json([
                'message' => 'Séance mise à jour.',
                'data' => $seance->fresh(['semaine', 'salle', 'module', 'formateur', 'groupe'])
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la mise à jour',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    public function destroy($id)
    {
        $seance = Seance::findOrFail($id);
        // Vérifier si l'utilisateur a le droit de supprimer une séance
        $currentUser = Auth::user();
        if (!Gate::forUser($currentUser)->allows('delete', $seance)) {
            return response()->json([
                'message' => "Vous n'avez pas le droit de supprimer cette séance.",
            ], 403);
        }
        $seance->delete();

        return response()->json([
            'message' => 'Séance supprimée.'
        ], 200);
    }
}
