<?php

namespace App\Http\Controllers;

use App\Models\Seance;
use App\Models\Semaine;
use App\Models\Ferie;
use App\Models\Groupe;
use App\Models\SectEfp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Barryvdh\DomPDF\Facade\Pdf;

class SeanceController extends Controller
{
    // Constantes pour les horaires des séances
    const PREMIERE_SEANCE_DEBUT = '08:30';
    const PREMIERE_SEANCE_FIN_AVANT_PAUSE = '10:50';
    const PREMIERE_SEANCE_DEBUT_APRES_PAUSE = '11:10';
    const PREMIERE_SEANCE_FIN = '13:30';

    const DEUXIEME_SEANCE_DEBUT = '13:30';
    const DEUXIEME_SEANCE_FIN_AVANT_PAUSE = '15:50';
    const DEUXIEME_SEANCE_DEBUT_APRES_PAUSE = '16:10';
    const DEUXIEME_SEANCE_FIN = '18:30';

    const DUREE_PAUSE = 20; // minutes

    public function exportEmploiDuTemps($selectedSecteur)
    {
        $user = Auth::user();

        if ($user->role === 'DirecteurEtablissement') {
            $etablissement = $user->directeurEtablissement->etablissement;
        } elseif ($user->role === 'Formateur') {
            $etablissement = $user->formateur->etablissement;
        } else {
            return response()->json(['message' => 'Accès non autorisé'], 403);
        }

        $secteurId = $selectedSecteur;
        $secteurNom = null;

        $semaine = Semaine::with('anneeScolaire', 'etablissement')
            ->where('etablissement_id', $etablissement->id)
            ->orderBy('date_fin', 'desc')
            ->first();

        if (!$semaine) {
            return response()->json(['message' => 'Aucune semaine trouvée.'], 404);
        }

        $seancesQuery = Seance::with([
            'module.filiere',
            'formateur.utilisateur',
            'salle',
            'groupe'
        ])
            ->where('semaine_id', $semaine->id)
            ->orderBy('date_seance')
            ->whereHas('semaine', function ($query) use ($etablissement) {
                $query->where('etablissement_id', $etablissement->id);
            });

        if ($secteurId && $secteurId !== 'all') {
            $secteur = SectEfp::where('etablissement_id', $etablissement->id)
                ->where('secteur_id', $secteurId)
                ->with('secteur')
                ->first();

            if (!$secteur) {
                return response()->json(['message' => 'Secteur non trouvé pour cet établissement'], 404);
            }
            $secteurNom = $secteur->secteur->nom;

            $seancesQuery->whereHas('module.filiere', function ($query) use ($secteurId) {
                $query->where('secteur_id', $secteurId);
            });
        }

        $seances = $seancesQuery->get();
        $groupes = Groupe::where('etablissement_id', $etablissement->id)
            ->whereHas('filiere', function ($query) use ($secteurId) {
                $query->where('secteur_id', $secteurId);
            });

        $pdf = Pdf::loadView('pdf.emploi_du_temps', [
            'seances' => $seances,
            'etablissement' => $etablissement,
            'semaine' => $semaine,
            'secteurId' => $secteurId,
            'secteurNom' => $secteurNom,
            'groupes' => $groupes
        ]);

        $pdf->setPaper('A4', 'landscape');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'dpi' => 150,
            'defaultFont' => 'Arial',
            'scale' => 0.8,
        ]);

        return $pdf->download('emploi_du_temps.pdf');
    }
    public function exportEmploiDuTempsFormateur(Request $request)
    {
        $user = Auth::user();

        if ($user->role !== 'Formateur') {
            return response()->json(['message' => 'Accès non autorisé'], 403);
        }

        $formateur = $user->formateur;

        $semaineId = $request->input('semaine_id');

        $semaine = Semaine::find($semaineId);
        if (!$semaine) {
            return response()->json(['message' => 'Semaine non trouvée'], 404);
        }

        $seances = Seance::with(['module', 'salle', 'groupe'])
            ->where('formateur_id', $formateur->id)
            ->where('semaine_id', $semaine->id)
            ->orderBy('date_seance')
            ->orderBy('heure_debut')
            ->get();

        $pdf = Pdf::loadView('pdf.emploi_du_temps_formateur', [
            'seances' => $seances,
            'formateur' => $formateur,
            'semaine' => $semaine,
        ]);

        return $pdf->download("emploi_du_temps_formateur_semaine_{$semaine->numero_semaine}.pdf");
    }
    public function getSeancesByWeek(Request $request, $weekId)
    {
        $user = Auth::user();

        if ($user->role !== 'Formateur') {
            return response()->json(['message' => 'Accès non autorisé'], 403);
        }

        $semaine = Semaine::with('anneeScolaire')->findOrFail($weekId);
        $etablissement = $user->formateur->etablissement;

        // Vérifier que la semaine appartient à l'établissement du formateur
        if ($semaine->etablissement_id !== $etablissement->id) {
            return response()->json(['message' => 'Accès non autorisé à cette semaine'], 403);
        }

        $seances = Seance::with(['semaine', 'salle', 'module', 'groupe', 'formateur'])
            ->where('formateur_id', $user->formateur->id)
            ->where('semaine_id', $weekId)
            ->get();

        return response()->json([
            'message' => 'Séances récupérées avec succès.',
            'data' => $seances,
            'semaine' => $semaine,
        ], 200);
    }
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
        // Vérification si la date de séance tombe sur un jour férié
        $isFerie = Ferie::where('date_debut', '<=', $validated['date_seance'])
            ->where('date_fin', '>', $validated['date_seance'])
            ->exists();

        if ($isFerie) {
            $jourFerie = Ferie::where('date_debut', '<=', $validated['date_seance'])
                ->where('date_fin', '>', $validated['date_seance'])
                ->first();
            return response()->json([
                'message' => 'Impossible de programmer une séance un jour férié. (Motif : ' . $jourFerie->nom . ')',
            ], 422);
        }

        // Vérification des horaires selon les règles de l'OFPPM
        if (!$this->validateSeanceHours($validated['heure_debut'], $validated['heure_fin'])) {
            return response()->json([
                'message' => 'Les horaires de la séance ne respectent pas le format imposé par l\'OFPPT.',
                'valid_hours' => [
                    'premiere_seance' => [
                        '08:30-10:50 (pause) 11:10-13:30'
                    ],
                    'deuxieme_seance' => [
                        '13:30-15:50 (pause) 16:10-18:30'
                    ]
                ]
            ], 422);
        }

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

        // Vérification de la disponibilité du formateur
        $existingFormateurConflict = Seance::where('formateur_id', $validated['formateur_id'])
            ->where('date_seance', $validated['date_seance'])
            ->where(function ($query) use ($validated) {
                $query->where('heure_debut', $validated['heure_debut'])
                    ->where('heure_fin', $validated['heure_fin']);
            })
            ->whereHas('semaine', function ($query) use ($etablissement) {
                $query->where('etablissement_id', $etablissement->id);
            })
            ->where(function ($query) use ($validated) {
                $query->where('type', 'presentiel') // conflit si au moins une séance est en présentiel
                    ->orWhere('type', '<>', $validated['type']); // ou s’ils sont de types différents
            })
            ->exists();

        if ($existingFormateurConflict) {
            return response()->json([
                'message' => 'Le formateur est déjà occupé à cette date et heure.',
            ], 422);
        }


        // Vérification pour la salle (si présente)
        if ($validated['salle_id']) {
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
        }
        // Vérification de la disponibilité du groupe
        $existingGroupe = Seance::where('groupe_id', $validated['groupe_id'])
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

        if ($existingGroupe) {
            return response()->json([
                'message' => 'Le groupe a déjà une séance programmée à cette date et heure.',
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

        // Vérification des horaires si ils sont modifiés
        if (isset($validated['heure_debut']) || isset($validated['heure_fin'])) {
            $heureDebut = $validated['heure_debut'] ?? $seance->heure_debut;
            $heureFin = $validated['heure_fin'] ?? $seance->heure_fin;

            if (!$this->validateSeanceHours($heureDebut, $heureFin)) {
                return response()->json([
                    'message' => 'Les horaires de la séance ne respectent pas le format imposé par l\'OFPPT.',
                    'valid_hours' => [
                        'premiere_seance' => [
                            '08:30-10:50 (pause) 11:10-13:30'
                        ],
                        'deuxieme_seance' => [
                            '13:30-15:50 (pause) 16:10-18:30'
                        ]
                    ]
                ], 422);
            }
        }

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

            $existingFormateurConflict = Seance::where('formateur_id', $formateurId)
                ->where('date_seance', $dateSeance)
                ->where('id', '!=', $seance->id)
                ->where(function ($query) use ($heureDebut, $heureFin) {
                    $query->where('heure_debut', $heureDebut)
                        ->where('heure_fin', $heureFin);
                })
                ->whereHas('semaine', function ($query) use ($etablissement) {
                    $query->where('etablissement_id', $etablissement->id);
                })
                ->where(function ($query) use ($validated, $seance) {
                    $type = $validated['type'] ?? $seance->type;
                    $query->where('type', 'presentiel')
                        ->orWhere('type', '<>', $type);
                })
                ->exists();

            if ($existingFormateurConflict) {
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
                ->where('id', '!=', $seance->id)
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

        // verification de disponibilité du groupe
        if (isset($validated['groupe_id']) || isset($validated['date_seance']) || isset($validated['heure_debut']) || isset($validated['heure_fin'])) {
            $groupeId = $validated['groupe_id'] ?? $seance->groupe_id;
            $dateSeance = $validated['date_seance'] ?? $seance->date_seance;
            $heureDebut = $validated['heure_debut'] ?? $seance->heure_debut;
            $heureFin = $validated['heure_fin'] ?? $seance->heure_fin;

            $existingGroupe = Seance::where('groupe_id', $groupeId)
                ->where('date_seance', $dateSeance)
                ->where('id', '!=', $seance->id)
                ->where(function ($query) use ($heureDebut, $heureFin) {
                    $query->where(function ($q) use ($heureDebut, $heureFin) {
                        $q->where('heure_debut', '=', $heureDebut)
                            ->where('heure_fin', '=', $heureFin);
                    });
                })->whereHas('semaine', function ($query) use ($etablissement) {
                    $query->where('etablissement_id', $etablissement->id);
                })
                ->exists();

            if ($existingGroupe) {
                return response()->json([
                    'message' => 'Le groupe a déjà une séance programmée à cette date et heure.',
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

    /**
     * Valide que les heures de début et fin correspondent aux créneaux OFPPM
     */
    private function validateSeanceHours($heureDebut, $heureFin)
    {
        // Première séance avant pause
        $isPremiereAvantPause = ($heureDebut === self::PREMIERE_SEANCE_DEBUT && $heureFin === self::PREMIERE_SEANCE_FIN_AVANT_PAUSE);

        // Première séance après pause
        $isPremiereApresPause = ($heureDebut === self::PREMIERE_SEANCE_DEBUT_APRES_PAUSE && $heureFin === self::PREMIERE_SEANCE_FIN);

        // Deuxième séance avant pause
        $isDeuxiemeAvantPause = ($heureDebut === self::DEUXIEME_SEANCE_DEBUT && $heureFin === self::DEUXIEME_SEANCE_FIN_AVANT_PAUSE);

        // Deuxième séance après pause
        $isDeuxiemeApresPause = ($heureDebut === self::DEUXIEME_SEANCE_DEBUT_APRES_PAUSE && $heureFin === self::DEUXIEME_SEANCE_FIN);

        return $isPremiereAvantPause || $isPremiereApresPause || $isDeuxiemeAvantPause || $isDeuxiemeApresPause;
    }
}
