<?php

namespace App\Http\Controllers;

use App\Models\Etablissement;
use App\Models\Salle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class SalleController extends Controller
{
    public function index()
    {
        $currentUser = Auth::user();

        // Vérification de l'autorisation
        if (!Gate::forUser($currentUser)->allows('view', Salle::class)) {
            return response()->json([
                'message' => "Vous n'avez pas le droit de voir la liste des salles.",
            ], 403);
        }

        // Récupération de l'établissement à partir du directeur
        $directeur = $currentUser->directeurEtablissement;
        $etablissement = $directeur ? $directeur->etablissement()->first() : null;

        if (!$etablissement) {
            return response()->json([
                'message' => "Aucun établissement associé à cet utilisateur.",
            ], 404);
        }

        $salles = $etablissement->salles()->with('etablissement')->get();

        return response()->json([
            'message' => 'Liste des salles récupérée avec succès.',
            'salles' => $salles,
            'etablissement' => $etablissement,  
        ], 200);

    }



    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string',
            'capacite' => 'required|integer|min:1',
            'type' => 'required|in:Salle,Atelier',
            'etablissement_id' => 'required|exists:etablissements,id',
        ]);
        // Vérifier si l'utilisateur a le droit de créer une salle
        $currentUser = Auth::user();
        if (!Gate::forUser($currentUser)->allows('create', Salle::class)) {
            return response()->json([
                'message' => "Vous n'avez pas le droit de créer une salle.",
            ], 403);
        }

        $salle = Salle::create($validated);

        return response()->json([
            'message' => 'Salle créée avec succès.',
            'data' => $salle
        ], 201);
    }

    public function show($id)
    {
        $salle = Salle::with('etablissement')->findOrFail($id);
        // Vérifier si l'utilisateur a le droit de voir les détails d'une salle
        $currentUser = Auth::user();
        if (!Gate::forUser($currentUser)->allows('viewAny', $salle)) {
            return response()->json([
                'message' => "Vous n'avez pas le droit de voir les détails de cette salle.",
            ], 403);
        }
        return response()->json([
            'message' => 'Salle récupérée avec succès.',
            'data' => $salle,
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $salle = Salle::findOrFail($id);
        $validated = $request->validate([
            'nom' => 'sometimes|required|string',
            'capacite' => 'sometimes|required|integer|min:1',
            'type' => 'sometimes|required|in:Salle,Atelier',
            'etablissement_id' => 'sometimes|required|exists:etablissements,id',
        ]);
        // Vérifier si l'utilisateur a le droit de mettre à jour une salle
        $currentUser = Auth::user();
        if (!Gate::forUser($currentUser)->allows('update', $salle)) {
            return response()->json([
                'message' => "Vous n'avez pas le droit de mettre à jour cette salle.",
            ], 403);
        }

        $salle->update($validated);

        return response()->json([
            'message' => 'Salle mise à jour.',
            'data' => $salle->fresh(['etablissement'])
        ], 200);
    }

    public function destroy($id)
    {
        $salle = Salle::findOrFail($id);
        // Vérifier si l'utilisateur a le droit de supprimer une salle
        $currentUser = Auth::user();
        if (!Gate::forUser($currentUser)->allows('delete', $salle)) {
            return response()->json([
                'message' => "Vous n'avez pas le droit de supprimer cette salle.",
            ], 403);
        }
        $salle->delete();

        return response()->json([
            'message' => 'Salle supprimée.'
        ], 200);
    }
}
