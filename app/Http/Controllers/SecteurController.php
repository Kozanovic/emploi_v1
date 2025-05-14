<?php

namespace App\Http\Controllers;

use App\Models\Secteur;
use Illuminate\Http\Request;

class SecteurController extends Controller
{
    public function index()
    {
        //with('etablissements')->get()
        $secteurs = Secteur::all();
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
        $secteur = Secteur::create($validated);
        return response()->json([
            'message' => 'Secteur créé avec succès', 
            'data' => $secteur
        ]);
    }

    public function show($id)
    {
        //with('etablissements')->findOrFail($id);
        $secteur = Secteur::findOrFail($id);
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
        $secteur->delete();
        return response()->json([
            'message' => 'Secteur supprimé avec succès'
        ]);
    }
}
