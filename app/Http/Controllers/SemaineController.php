<?php

namespace App\Http\Controllers;

use App\Models\AnneeScolaire;
use App\Models\Semaine;
use App\Models\Etablissement;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class SemaineController extends Controller
{
    public function index()
    {
        // Vérifier si l'utilisateur a le droit de voir la liste des modules
        $currentUser = Auth::user();
        if (!Gate::forUser($currentUser)->allows('viewAny', Semaine::class)) {
            return response()->json([
                'message' => "Vous n'avez pas le droit de voir la liste des semaines.",
            ], 403);
        }

        $etablissement = null;

        if ($currentUser->role === 'DirecteurEtablissement') {
            $etablissement = Etablissement::where('directeur_etablissement_id', $currentUser->directeurEtablissement->id)->first();
        } elseif ($currentUser->role === 'Formateur') {
            $etablissement = $currentUser->formateur->etablissement;
        }

        if (!$etablissement) {
            return response()->json([
                'message' => "Aucun établissement trouvé.",
            ], 400);
        }

        $semaines = Semaine::with('anneeScolaire', 'etablissement')
            ->where('etablissement_id', $etablissement->id)
            ->orderByDesc('numero_semaine')
            ->get();
        $annees = AnneeScolaire::all();
        return response()->json([
            'message' => 'Liste des semaines récupérée avec succès.',
            'data' => $semaines,
            'annees' => $annees,
        ], 200);
    }

    public function filterByWeek($semaineId)
    {
        $currentUser = Auth::user();

        if ($currentUser->role === 'DirecteurEtablissement') {
            $etablissement = Etablissement::where('directeur_etablissement_id', $currentUser->directeurEtablissement->id)->first();
        } elseif ($currentUser->role === 'Formateur' && $currentUser->formateur->peut_gerer_seance) {
            $etablissement = Etablissement::where('id', $currentUser->formateur->etablissement->id)->first();
        } else {
            return response()->json([
                'message' => "Vous n'avez pas le droit de voir la liste des semaines pour cet établissement.",
            ], 403);
        }

        $semaine = Semaine::with('anneeScolaire', 'etablissement')
            ->where('etablissement_id', $etablissement->id)
            ->where('id', $semaineId)
            ->get();
        $seances = $semaine->seances()->with(['module', 'groupe', 'formateur', 'salle'])
            ->whereHas('semaine', function ($query) use ($etablissement) {
                $query->where('etablissement_id', $etablissement->id);
            })->get();
        return response()->json([
            'seances' => $seances,
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

        $currentUser = Auth::user();

        if (!Gate::forUser($currentUser)->allows('create', Semaine::class)) {
            return response()->json([
                'message' => "Vous n'avez pas le droit de créer une semaine.",
            ], 403);
        }

        if ($currentUser->role === 'DirecteurEtablissement') {
            $etablissement = Etablissement::where('directeur_etablissement_id', $currentUser->directeurEtablissement->id)->first();
        } elseif ($currentUser->role === 'Formateur' && $currentUser->formateur->peut_gerer_seance) {
            $etablissement = Etablissement::where('id', $currentUser->formateur->etablissement->id)->first();
        } else {
            return response()->json([
                'message' => "Vous n'avez pas le droit de voir la liste des semaines pour cet établissement.",
            ], 403);
        }

        if (!$etablissement) {
            return response()->json([
                'message' => "Aucun établissement trouvé pour ce directeur.",
            ], 400);
        }

        $semaine = Semaine::create(array_merge($validated, [
            'etablissement_id' => $etablissement->id,
        ]));

        return response()->json([
            'message' => 'Semaine créée avec succès.',
            'data' => $semaine->load(['anneeScolaire', 'etablissement']),
        ], 201);
    }


    public function show($id)
    {
        $semaine = Semaine::with(['anneeScolaire', 'etablissement'])->findOrFail($id);
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

        $currentUser = Auth::user();

        if (!Gate::forUser($currentUser)->allows('update', Semaine::class)) {
            return response()->json([
                'message' => "Vous n'avez pas le droit de mettre à jour une semaine.",
            ], 403);
        }
        // Vérifie que la semaine appartient à l’établissement du directeur
        if ($currentUser->role === 'DirecteurEtablissement') {
            $etablissement = Etablissement::where('directeur_etablissement_id', $currentUser->directeurEtablissement->id)->first();
        } elseif ($currentUser->role === 'Formateur' && $currentUser->formateur->peut_gerer_seance) {
            $etablissement = Etablissement::where('id', $currentUser->formateur->etablissement->id)->first();
        } else {
            return response()->json([
                'message' => "Vous n'avez pas le droit de voir la liste des semaines pour cet établissement.",
            ], 403);
        }

        if (!$etablissement || $semaine->etablissement_id !== $etablissement->id) {
            return response()->json([
                'message' => "Vous ne pouvez modifier que les semaines de votre établissement.",
            ], 403);
        }

        $semaine->update(array_merge($validated, [
            'etablissement_id' => $etablissement->id,
        ]));

        return response()->json([
            'message' => 'Semaine mise à jour avec succès.',
            'data' => $semaine->fresh(['anneeScolaire', 'etablissement']),
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
