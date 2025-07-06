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
        if ($currentUser->role === 'DirecteurEtablissement') {
            $etablissement = $currentUser->directeurEtablissement->etablissement;
        } elseif ($currentUser->role === 'Formateur' && $currentUser->formateur->peut_gerer_seance) {
            $etablissement = $currentUser->formateur->etablissement;
        } else {
            return response()->json(['message' => 'Accès non autorisé'], 403);
        }
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
            'heure_debut' => 'required',
            'heure_fin' => 'required|after:heure_debut',
            'type' => 'required|in:presentiel,distanciel',
            'semaine_id' => 'required|exists:semaines,id',
            'salle_id' => 'nullable|exists:salles,id',
            'module_id' => 'required|exists:modules,id',
            'formateur_id' => 'required|exists:formateurs,id',
            'groupe_id' => 'required|exists:groupes,id',
        ]);

        // Vérifier si l'utilisateur a le droit de créer une séance
        $currentUser = Auth::user();
        $isFormateur = $currentUser->role === 'Formateur' && $currentUser->formateur->peut_gerer_seance;

        if (!Gate::forUser($currentUser)->allows('create', Seance::class)) {
            return response()->json([
                'message' => "Vous n'avez pas le droit de créer une séance.",
            ], 403);
        }

        // Récupérer l'établissement selon le rôle
        if ($currentUser->role === 'DirecteurEtablissement') {
            $etablissement = $currentUser->directeurEtablissement->etablissement;
        } elseif ($isFormateur) {
            $etablissement = $currentUser->formateur->etablissement;
        } else {
            return response()->json(['message' => 'Accès non autorisé'], 403);
        }
        // Vérification de la disponibilité de la salle
        $existingFormateur = Seance::where('formateur_id', $validated['formateur_id'])
            ->where('date_seance', $validated['date_seance'])
            ->where(function ($query) use ($validated) {
                // Empêche les chevauchements dans la même plage horaire
                $query->where(function ($q) use ($validated) {
                    $q->where('heure_debut', '=', $validated['heure_debut'])
                        ->where('heure_fin', '=', $validated['heure_fin']);
                });
            })->whereHas('semaine', function ($query) use ($etablissement) {
                $query->where('etablissement_id', $etablissement->id);
            })
            ->exists();

        if ($existingFormateur) {
            return response()->json([
                'message' => 'Le formateur est déjà occupé à cette date et heure.',
            ], 422);
        }

        // Vérification pour la salle
        $existingSalle = Seance::where('salle_id', $validated['salle_id'])
            ->where('date_seance', $validated['date_seance'])
            ->where(function ($query) use ($validated) {
                $query->where(function ($q) use ($validated) {
                    $q->where('heure_debut', '=', $validated['heure_debut'])
                        ->where('heure_fin', '=', $validated['heure_fin']);
                });
            })->whereHas('semaine', function ($query) use ($etablissement) {
                $query->where('etablissement_id', $etablissement->id);
            })
            ->exists();

        if ($existingSalle) {
            return response()->json([
                'message' => 'La salle est déjà occupée à cette date et heure.',
            ], 422);
        }

        // Création de la séance
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
            'salle_id' => 'sometimes|nullable|exists:salles,id',
            'module_id' => 'sometimes|required|exists:modules,id',
            'formateur_id' => 'sometimes|required|exists:formateurs,id',
            'groupe_id' => 'sometimes|required|exists:groupes,id',
        ]);

        // Vérification des permissions
        $currentUser = Auth::user();
        $isFormateur = $currentUser->role === 'Formateur' && $currentUser->formateur->peut_gerer_seance;

        if (!Gate::forUser($currentUser)->allows('create', Seance::class)) {
            return response()->json([
                'message' => "Vous n'avez pas le droit de créer une séance.",
            ], 403);
        }

        // Récupérer l'établissement selon le rôle
        if ($currentUser->role === 'DirecteurEtablissement') {
            $etablissement = $currentUser->directeurEtablissement->etablissement;
        } elseif ($isFormateur) {
            $etablissement = $currentUser->formateur->etablissement;
        } else {
            return response()->json(['message' => 'Accès non autorisé'], 403);
        }

        // Vérification de la disponibilité du formateur
        if (isset($validated['formateur_id']) || isset($validated['date_seance']) || isset($validated['heure_debut']) || isset($validated['heure_fin'])) {
            $formateurId = $validated['formateur_id'] ?? $seance->formateur_id;
            $dateSeance = $validated['date_seance'] ?? $seance->date_seance;
            $heureDebut = $validated['heure_debut'] ?? $seance->heure_debut;
            $heureFin = $validated['heure_fin'] ?? $seance->heure_fin;

            $existingFormateur = Seance::where('formateur_id', $formateurId)
                ->where('date_seance', $dateSeance)
                ->where('id', '!=', $seance->id) // Exclure la séance actuelle
                ->where(function ($query) use ($heureDebut, $heureFin) {
                    $query->where(function ($q) use ($heureDebut, $heureFin) {
                        $q->where('heure_debut', '=', $heureDebut)
                            ->where('heure_fin', '=', $heureFin);
                    });
                })->whereHas('semaine', function ($query) use ($etablissement) {
                    $query->where('etablissement_id', $etablissement->id);
                })
                ->exists();

            if ($existingFormateur) {
                return response()->json([
                    'message' => 'Le formateur est déjà occupé à cette date et heure.',
                ], 422);
            }
        }

        // Vérification de la disponibilité de la salle
        if (isset($validated['salle_id'])) {
            $salleId = $validated['salle_id'];
            $dateSeance = $validated['date_seance'] ?? $seance->date_seance;
            $heureDebut = $validated['heure_debut'] ?? $seance->heure_debut;
            $heureFin = $validated['heure_fin'] ?? $seance->heure_fin;

            $existingSalle = Seance::where('salle_id', $salleId)
                ->where('date_seance', $dateSeance)
                ->where('id', '!=', $seance->id) // Exclure la séance actuelle
                ->where(function ($query) use ($heureDebut, $heureFin) {
                    $query->where(function ($q) use ($heureDebut, $heureFin) {
                        $q->where('heure_debut', '=', $heureDebut)
                            ->where('heure_fin', '=', $heureFin);
                    });
                })->whereHas('semaine', function ($query) use ($etablissement) {
                    $query->where('etablissement_id', $etablissement->id);
                })
                ->exists();

            if ($existingSalle) {
                return response()->json([
                    'message' => 'La salle est déjà occupée à cette date et heure.',
                ], 422);
            }
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
