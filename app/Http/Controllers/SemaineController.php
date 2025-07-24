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

        $semaine = Semaine::where('etablissement_id', $etablissement->id)
            ->orderByDesc('numero_semaine')
            ->first();
        $annees = AnneeScolaire::all();
        return response()->json([
            'message' => 'Liste des semaines récupérée avec succès.',
            'data' => $semaines,
            'annees' => $annees,
            'annee_scolaire_nom' => $semaine ? $semaine->anneeScolaire->nom : null,
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

        // S'assurer que date_debut est un lundi et date_fin un samedi
        $dayStart = \Carbon\Carbon::parse($validated['date_debut'])->dayOfWeek;
        $dayEnd = \Carbon\Carbon::parse($validated['date_fin'])->dayOfWeek;

        if ($dayStart !== \Carbon\Carbon::MONDAY) {
            return response()->json([
                'message' => "La date de début doit être un lundi.",
            ], 400);
        }

        if ($dayEnd !== \Carbon\Carbon::SATURDAY) {
            return response()->json([
                'message' => "La date de fin doit être un samedi.",
            ], 400);
        }

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

        $latestWeek = Semaine::where('etablissement_id', $etablissement->id)
            ->where('annee_scolaire_id', $validated['annee_scolaire_id'])
            ->orderByDesc('date_fin')
            ->first();

        $existingWeek = Semaine::where('etablissement_id', $etablissement->id)
            ->where('annee_scolaire_id', $validated['annee_scolaire_id'])
            ->where('numero_semaine', $validated['numero_semaine'])
            ->exists();

        if ($existingWeek) {
            return response()->json([
                'message' => "Une semaine avec le même numéro existe déjà pour cet établissement et cette année scolaire.",
            ], 400);
        }

        if ($latestWeek) {
            if ($validated['date_debut'] <= $latestWeek->date_fin) {
                return response()->json([
                    'message' => "La date de début de la semaine doit être postérieure à la dernière semaine enregistrée.",
                ], 400);
            }

            if ($validated['date_fin'] <= $latestWeek->date_fin) {
                return response()->json([
                    'message' => "La date de fin de la semaine doit être postérieure à la dernière semaine enregistrée.",
                ], 400);
            }
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

        // Vérification de l'établissement selon le rôle
        if ($currentUser->role === 'DirecteurEtablissement') {
            $etablissement = Etablissement::where('directeur_etablissement_id', $currentUser->directeurEtablissement->id)->first();
        } elseif ($currentUser->role === 'Formateur' && $currentUser->formateur->peut_gerer_seance) {
            $etablissement = Etablissement::where('id', $currentUser->formateur->etablissement->id)->first();
        } else {
            return response()->json([
                'message' => "Vous n'avez pas le droit de voir ou modifier les semaines pour cet établissement.",
            ], 403);
        }

        if (!$etablissement || $semaine->etablissement_id !== $etablissement->id) {
            return response()->json([
                'message' => "Vous ne pouvez modifier que les semaines de votre établissement.",
            ], 403);
        }

        // Vérifier que date_debut est un lundi et date_fin est un samedi
        if (isset($validated['date_debut'])) {
            $dayStart = \Carbon\Carbon::parse($validated['date_debut'])->dayOfWeek;
            if ($dayStart !== \Carbon\Carbon::MONDAY) {
                return response()->json([
                    'message' => "La date de début doit être un lundi.",
                ], 400);
            }
        }

        if (isset($validated['date_fin'])) {
            $dayEnd = \Carbon\Carbon::parse($validated['date_fin'])->dayOfWeek;
            if ($dayEnd !== \Carbon\Carbon::SATURDAY) {
                return response()->json([
                    'message' => "La date de fin doit être un samedi.",
                ], 400);
            }
        }

        // Vérification de l’unicité du numero_semaine pour le même établissement et la même année
        if (
            isset($validated['numero_semaine']) &&
            isset($validated['annee_scolaire_id']) &&
            ($validated['numero_semaine'] != $semaine->numero_semaine ||
                $validated['annee_scolaire_id'] != $semaine->annee_scolaire_id)
        ) {
            $exists = Semaine::where('etablissement_id', $etablissement->id)
                ->where('annee_scolaire_id', $validated['annee_scolaire_id'])
                ->where('numero_semaine', $validated['numero_semaine'])
                ->where('id', '!=', $semaine->id)
                ->exists();

            if ($exists) {
                return response()->json([
                    'message' => "Une semaine avec le même numéro existe déjà pour cet établissement et cette année scolaire.",
                ], 400);
            }
        }

        // Vérification que la nouvelle date est postérieure à la dernière semaine enregistrée (si applicable)
        if (isset($validated['date_debut']) || isset($validated['date_fin'])) {
            $latestWeek = Semaine::where('etablissement_id', $etablissement->id)
                ->where('annee_scolaire_id', $validated['annee_scolaire_id'] ?? $semaine->annee_scolaire_id)
                ->where('id', '!=', $semaine->id)
                ->orderByDesc('date_fin')
                ->first();

            if ($latestWeek) {
                if (
                    isset($validated['date_debut']) &&
                    $validated['date_debut'] <= $latestWeek->date_debut
                ) {
                    return response()->json([
                        'message' => "La date de début de la semaine doit être postérieure à la dernière semaine enregistrée.",
                    ], 400);
                }

                if (
                    isset($validated['date_fin']) &&
                    $validated['date_fin'] <= $latestWeek->date_fin
                ) {
                    return response()->json([
                        'message' => "La date de fin de la semaine doit être postérieure à la dernière semaine enregistrée.",
                    ], 400);
                }
            }
        }

        // Mise à jour
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
