<?php

namespace App\Http\Controllers;

use App\Models\Secteur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class SecteurController extends Controller
{
    public function index()
    {
        $secteurs = Secteur::all();

        // Vérifier si l'utilisateur a le droit de voir la liste des secteurs
        $currentUser = Auth::user();
        if (!Gate::forUser($currentUser)->allows('view', Secteur::class)) {
            return response()->json([
                'message' => "Vous n'avez pas le droit de voir la liste des secteurs.",
            ], 403);
        }

        return response()->json([
            'message' => 'Liste des secteurs récupérée avec succès.',
            'data' => $secteurs,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255'
        ]);
        // Vérifier si l'utilisateur a le droit de créer un secteur
        $currentUser = Auth::user();
        if (!Gate::forUser($currentUser)->allows('create', Secteur::class)) {
            return response()->json([
                'message' => "Vous n'avez pas le droit de créer un secteur.",
            ], 403);
        }
        // Créer le secteur
        $secteur = Secteur::create($validated);
        return response()->json([
            'message' => 'Secteur créé avec succès',
            'data' => $secteur
        ]);
    }

    public function show($id)
    {
        // Vérifier si l'utilisateur a le droit de voir un secteur
        $secteur = Secteur::findOrFail($id);
        $currentUser = Auth::user();
        if (!Gate::forUser($currentUser)->allows('viewAny', $secteur)) {
            return response()->json([
                'message' => "Vous n'avez pas le droit de voir ce secteur.",
            ], 403);
        }
        return response()->json([
            'data' => $secteur
        ]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255'
        ]);
        $secteur = Secteur::findOrFail($id);
        // Vérifier si l'utilisateur a le droit de mettre à jour un secteur
        $currentUser = Auth::user();
        if (!Gate::forUser($currentUser)->allows('update', $secteur)) {
            return response()->json([
                'message' => "Vous n'avez pas le droit de mettre à jour ce secteur.",
            ], 403);
        }
        // Mettre à jour le secteur
        $secteur->update($validated);
        //->fresh('etablissements')
        return response()->json([
            'message' => 'Secteur mis à jour',
            'data' => $secteur
        ]);
    }

    public function destroy($id)
    {
        $secteur = Secteur::findOrFail($id);
        // Vérifier si l'utilisateur a le droit de supprimer un secteur
        $currentUser = Auth::user();
        if (!Gate::forUser($currentUser)->allows('delete', $secteur)) {
            return response()->json([
                'message' => "Vous n'avez pas le droit de supprimer ce secteur.",
            ], 403);
        }
        $secteur->delete();
        return response()->json([
            'message' => 'Secteur supprimé avec succès'
        ]);
    }
}
