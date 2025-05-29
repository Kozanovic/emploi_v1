<?php

namespace App\Http\Controllers;

use App\Models\Etablissement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Models\Complexe;

class EtablissementController extends Controller
{
    /**
     * Affiche la liste des établissements
     */
    public function index()
    {
        // Vérification des autorisations
        $currentUser = Auth::user();
        if (!Gate::forUser($currentUser)->allows('view', Etablissement::class)) {
            return response()->json(['message' => 'Non autorisé à voir la liste des établissements.'], 403);
        }
        // Récupération de tous les établissements
        $etablissements = Etablissement::with(['directeurEtablissement'])->get();
        $directeurEtablissement = Etablissement::where('directeur_etablissement_id', $currentUser->id)->get();
        $utilisateurs = User::where('role', 'DirecteurEtablissement')->get();
        $complexes = complexe::all();
        return response()->json([
            'message' => 'Liste des établissements récupérée avec succès',
            'data' => $etablissements,
            'directeurEtablissement' => $directeurEtablissement,
            'utilisateurs' => $utilisateurs,
            'complexes' => $complexes,
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
            'directeur_etablissement_id' => 'required',
            'complexe_id' => 'required|exists:complexes,id',
        ]);
        // Vérification des autorisations
        if (!Gate::allows('create', Etablissement::class)) {
            return response()->json(['message' => 'Non autorisé à créer un établissement.'], 403);
        }
        // Création de l'établissement

        $etablissement = Etablissement::create($data);

        return response()->json([
            'message' => 'Etablissement créé avec succès',
            'data' => $etablissement->load(['directeurEtablissement']),
        ], 201);
    }

    /**
     * Affiche un établissement spécifique
     */
    public function show($id)
    {
        $etablissement = Etablissement::findOrFail($id);
        // Vérification des autorisations
        if (!Gate::allows('view', $etablissement)) {
            return response()->json(['message' => 'Non autorisé à voir cet établissement.'], 403);
        }
        // Récupération de l'établissement avec le directeur
        return response()->json([
            'message' => 'Etablissement récupéré avec succès',
            'data' => $etablissement->load(['directeurEtablissement']),
        ]);
    }

    /**
     * Met à jour un établissement
     */
    public function update(Request $request, $id)
    {
        $etablissement = Etablissement::findOrFail($id);
        $data = $request->validate([
            'nom' => 'sometimes|string|max:255',
            'adresse' => 'sometimes|string',
            'telephone' => 'sometimes|string',
            'directeur_etablissement_id' => 'sometimes|required',
            'complexe_id' => 'sometimes|exists:complexes,id',
        ]);
        // Vérification des autorisations
        if (!Gate::allows('update', $etablissement)) {
            return response()->json(['message' => 'Non autorisé à mettre à jour cet établissement.'], 403);
        }
        // Mise à jour de l'établissement

        $etablissement->update($data);

        return response()->json([
            'message' => 'Etablissement mis à jour avec succès',
            'data' => $etablissement->fresh('directeurEtablissement'),
        ]);
    }

    /**
     * Supprime un établissement
     */
    public function destroy($id)
    {
        $etablissement = Etablissement::findOrFail($id);
        // Vérification des autorisations
        if (!Gate::allows('delete', $etablissement)) {
            return response()->json(['message' => 'Non autorisé à supprimer cet établissement.'], 403);
        }
        $etablissement->delete();

        return response()->json(null, 204);
    }
}
