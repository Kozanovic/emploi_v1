<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DirectionRegional;
use App\Models\Complexe;
use App\Models\Etablissement;
use App\Models\Groupe;
use App\Models\Offrir;
use App\Models\Seance;
use App\Models\SectEfp;
use App\Models\Semaine;
use Barryvdh\DomPDF\Facade\Pdf;

class StagiaireController extends Controller
{
    public function getDirectionRegionale()
    {
        $direction_regionales = DirectionRegional::orderBy('nom')->get();
        return response()->json([
            'data' => $direction_regionales
        ]);
    }
    public function getComplexe()
    {
        $complexes = Complexe::orderBy('nom')->get();
        return response()->json([
            'data' => $complexes
        ]);
    }
    public function getEtablissement()
    {
        $etablissements = Etablissement::orderBy('nom')->get();
        return response()->json([
            'data' => $etablissements,
        ]);
    }
    public function getGroupe()
    {
        $groupes = Groupe::orderBy('nom')->get();
        return response()->json([
            'data' => $groupes,
        ]);
    }
    public function getSecteurParEtablissement($etabId)
    {
        $secteurs = SectEfp::with(['secteur', 'etablissement'])->where('etablissement_id', $etabId)->get();
        return response()->json([
            'data' => $secteurs,
        ]);
    }
    public function getFiliere($etabId)
    {
        $filieres = Offrir::with(['filiere', 'etablissement'])->where('etablissement_id', $etabId)->get();
        return response()->json([
            'data' => $filieres,
        ]);
    }
    public function getSeance($etabId, $groupId)
    {
        $semaine = Semaine::with('anneeScolaire', 'etablissement')
            ->where('etablissement_id', $etabId)
            ->orderByDesc('date_fin')
            ->first();
        $seances = Seance::with(['semaine', 'salle', 'module', 'groupe', 'formateur.utilisateur'])
            ->where('semaine_id', $semaine->id)
            ->where('groupe_id', $groupId)
            ->get();
        return response()->json([
            'data' => $seances,
        ]);
    }
    public function exportEmploiDuTempsStagiaire(Request $request)
    {
        $groupeId = $request->input('groupe_id');
        $etablissementId = $request->input('etablissement_id');

        if (!$groupeId || !$etablissementId) {
            return response()->json(['message' => 'Paramètres manquants'], 400);
        }

        // Récupérer la semaine actuelle pour l'établissement
        $semaine = Semaine::with('anneeScolaire', 'etablissement')
            ->where('etablissement_id', $etablissementId)
            ->orderByDesc('date_fin')
            ->first();

        if (!$semaine) {
            return response()->json(['message' => 'Semaine non trouvée'], 404);
        }

        $seances = Seance::with(['semaine', 'module', 'salle', 'formateur.utilisateur', 'groupe'])
            ->where('semaine_id', $semaine->id)
            ->where('groupe_id', $groupeId)
            ->get();

        $groupe = Groupe::find($groupeId);

        $pdf = Pdf::loadView('pdf.emploi_du_temps_stagiaire', [
            'seances' => $seances,
            'groupe' => $groupe,
            'semaine' => $semaine,
        ]);

        return $pdf->download("emploi_du_temps_{$groupe->nom}_semaine_{$semaine->numero_semaine}.pdf");
    }
}
