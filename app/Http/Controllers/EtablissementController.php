<?php

namespace App\Http\Controllers;

use App\Models\Etablissement;
use App\Models\User as Utilisateur;
use Illuminate\Http\Request;

class EtablissementController extends Controller
{
    /**
     * Affiche la liste des établissements
     */
    public function index()
    {
        $directeurs = Utilisateur::where('role', 'Directeur')->get();
        return response()->json([
            'data' => Etablissement::with(['directeur'])->get(),
            'directeurs' => $directeurs,
            'message' => 'Liste des établissements récupérée avec succès'
        ]);
    }

    /**
     * Crée un nouvel établissement
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nom' => 'required|string|max:255',
            'adresse' => 'required|string',
            'telephone' => 'required|string',
            'directeur_id' => 'required|exists:utilisateurs,id'
        ]);

        $etablissement = Etablissement::create($data);

        return response()->json([
            'data' => $etablissement->load(['directeur']),
            'message' => 'Etablissement créé avec succès'
        ], 201);
    }

    /**
     * Affiche un établissement spécifique
     */
    public function show(Etablissement $etablissement)
    {
        return response()->json([
            'data' => $etablissement->load(['directeur']),
            'message' => 'Etablissement récupéré avec succès'
        ]);
    }

    /**
     * Met à jour un établissement
     */
    public function update(Request $request, Etablissement $etablissement)
    {
        $data = $request->validate([
            'nom' => 'sometimes|string|max:255',
            'adresse' => 'sometimes|string',
            'telephone' => 'sometimes|string',
            'directeur_id' => 'sometimes|exists:utilisateurs,id'
        ]);

        $etablissement->update($data);

        return response()->json([
            'data' => $etablissement->fresh('directeur'),
            'message' => 'Etablissement mis à jour avec succès'
        ]);
    }

    /**
     * Supprime un établissement
     */
    public function destroy(Etablissement $etablissement)
    {
        $etablissement->delete();

        return response()->json(null, 204);
    }
}
