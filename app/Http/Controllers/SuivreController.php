<?php

namespace App\Http\Controllers;

use App\Models\Suivre;
use App\Models\Module;
use App\Models\Groupe;
use Illuminate\Http\Request;

class SuivreController extends Controller
{
    public function index()
    {
        $suivres = Suivre::with(['module', 'groupe'])->get();
        $modules = Module::all();
        $groupes = Groupe::all();
        return response()->json([
            'data' => $suivres,
            'modules' => $modules,
            'groupes' => $groupes,
            'message' => 'Liste des suivis récupérée avec succès',
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'heure_effectue' => 'required|integer|min:1',
            'module_id' => 'required|exists:modules,id',
            'groupe_id' => 'required|exists:groupes,id',
        ]);

        $suivre = Suivre::create($request->all());

        return response()->json([
            'message' => 'Suivi créé avec succès',
            'data' => $suivre->fresh(['module', 'groupe'])
        ]);
    }

    public function show($id)
    {
        $suivre = Suivre::with(['module', 'groupe'])->findOrFail($id);

        return response()->json([
            'message' => 'Détail du suivi récupéré avec succès',
            'data' => $suivre
        ]);
    }

    public function update(Request $request, $id)
    {
        $suivre = Suivre::findOrFail($id);
        $request->validate([
            'heure_effectue' => 'required|integer|min:1',
            'module_id' => 'required|exists:modules,id',
            'groupe_id' => 'required|exists:groupes,id',
        ]);

        $suivre->update($request->all());

        return response()->json([
            'message' => 'Suivi mis à jour avec succès',
            'data' => $suivre->fresh(['module', 'groupe'])
        ]);
    }

    public function destroy($id)
    {
        $suivre = Suivre::findOrFail($id);
        $suivre->delete();

        return response()->json([
            'message' => 'Suivi supprimé avec succès'
        ]);
    }
}
