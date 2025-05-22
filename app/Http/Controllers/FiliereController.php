<?php

namespace App\Http\Controllers;

use App\Models\Filiere;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class FiliereController extends Controller
{
    public function index()
    {
        $filieres = Filiere::all();
        // Vérifier si l'utilisateur a le droit de voir la liste des filières
        $currentUser = Auth::user();
        if (!Gate::forUser($currentUser)->allows('view', Filiere::class)) {
            return response()->json([
                'message' => "Vous n'avez pas le droit de voir la liste des filières.",
            ], 403);
        }
        return response()->json([
            'message' => 'Liste des filières récupérée avec succès.',
            'data' => $filieres,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
        ]);
        // Vérifier si l'utilisateur a le droit de créer une filière
        $currentUser = Auth::user();
        if (!Gate::forUser($currentUser)->allows('create', Filiere::class)) {
            return response()->json([
                'message' => "Vous n'avez pas le droit de créer une filière.",
            ], 403);
        }

        $filiere = Filiere::create($validated);

        return response()->json([
            'message' => 'Filière créée avec succès.',
            'data' => $filiere,
        ]);
    }

    public function show($id)
    {
        $filiere = Filiere::findOrFail($id);
        // Vérifier si l'utilisateur a le droit de voir une filière
        $currentUser = Auth::user();
        if (!Gate::forUser($currentUser)->allows('viewAny', $filiere)) {
            return response()->json([
                'message' => "Vous n'avez pas le droit de voir cette filière.",
            ], 403);
        }
        return response()->json([
            'message' => 'Détails de la filière récupérés avec succès.',
            'data' => $filiere,
        ]);
    }

    public function update(Request $request, $id)
    {
        $filiere = Filiere::findOrFail($id);

        $validated = $request->validate([
            'nom' => 'sometimes|required|string|max:255',
        ]);
        // Vérifier si l'utilisateur a le droit de mettre à jour une filière
        $currentUser = Auth::user();
        if (!Gate::forUser($currentUser)->allows('update', $filiere)) {
            return response()->json([
                'message' => "Vous n'avez pas le droit de mettre à jour cette filière.",
            ], 403);
        }
        // Mettre à jour la filière

        $filiere->update($validated);

        return response()->json([
            'message' => 'Filière mise à jour avec succès.',
            'data' => $filiere,
        ]);
    }

    public function destroy($id)
    {
        $filiere = Filiere::findOrFail($id);
        // Vérifier si l'utilisateur a le droit de supprimer une filière
        $currentUser = Auth::user();
        if (!Gate::forUser($currentUser)->allows('delete', $filiere)) {
            return response()->json([
                'message' => "Vous n'avez pas le droit de supprimer cette filière.",
            ], 403);
        }
        $filiere->delete();

        return response()->json([
            'message' => 'Filière supprimée avec succès.'
        ]);
    }
}
