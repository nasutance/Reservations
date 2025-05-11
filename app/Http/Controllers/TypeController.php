<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Type;

class TypeController extends Controller
{
    /**
     * Affiche la liste de tous les types d'artistes.
     * Exemple : Scénographe, Comédien, Auteur, etc.
     */
    public function index()
    {
        // Récupère tous les types depuis la base de données
        $types = Type::all();

        // Envoie les données à la vue 'type.index' avec un nom de ressource
        return view('type.index', [
            'types' => $types,
            'resource' => 'types',
        ]);
    }

    /**
     * Affiche le formulaire de création d’un nouveau type.
     * À implémenter : renvoyer la vue avec un formulaire vide.
     */
    public function create()
    {
        // return view('type.create');
    }

    /**
     * Enregistre un nouveau type dans la base de données.
     * À implémenter : validation + insertion + redirection.
     */
    public function store(Request $request)
    {
        // Exemple :
        // $validated = $request->validate(['type' => 'required|max:60']);
        // Type::create($validated);
        // return redirect()->route('type.index');
    }

    /**
     * Affiche les détails d’un type spécifique.
     * Exemple : "Comédien" et les artistes liés (si relation ajoutée).
     */
    public function show(string $id)
    {
        $type = Type::find($id); // Pas de findOrFail ici : attention aux 404
        return view('type.show', [
            'type' => $type,
        ]);
    }

    /**
     * Affiche le formulaire d’édition pour un type donné.
     * À implémenter : passer le type à la vue.
     */
    public function edit(string $id)
    {
        // $type = Type::findOrFail($id);
        // return view('type.edit', ['type' => $type]);
    }

    /**
     * Met à jour les données d’un type existant.
     * À implémenter : validation + update + redirection.
     */
    public function update(Request $request, string $id)
    {
        // $validated = $request->validate(['type' => 'required|max:60']);
        // $type = Type::findOrFail($id);
        // $type->update($validated);
        // return redirect()->route('type.index');
    }

    /**
     * Supprime un type de la base de données.
     * À implémenter : suppression + redirection.
     */
    public function destroy(string $id)
    {
        // $type = Type::findOrFail($id);
        // $type->delete();
        // return redirect()->route('type.index');
    }
}
