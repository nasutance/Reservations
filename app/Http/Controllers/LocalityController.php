<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Locality;

class LocalityController extends Controller
{
    /**
     * Affiche la liste de toutes les localités.
     * Récupère tous les enregistrements de la table `localities` 
     * via le modèle Eloquent `Locality` et les passe à la vue `locality.index`.
     */
    public function index()
    {
        $localities = Locality::all(); // Récupère toutes les localités depuis la BD
        return view('locality.index', [
            'localities' => $localities, // Passe les données à la vue
        ]);
    }

    /**
     * Affiche le formulaire de création d'une nouvelle localité.
     * (À implémenter : retourne la vue contenant le formulaire).
     */
    public function create()
    {
        // TODO : retourner la vue 'locality.create'
    }

    /**
     * Enregistre une nouvelle localité dans la base de données.
     * (À implémenter : valider les données reçues et insérer l'enregistrement).
     */
    public function store(Request $request)
    {
        // TODO : valider les données du formulaire et créer une nouvelle localité
    }

    /**
     * Affiche les détails d'une localité spécifique.
     * Recherche une localité par son identifiant (clé primaire) 
     * et la passe à la vue `locality.show`.
     */
    public function show(string $id)
    {
        $locality = Locality::find($id); // Recherche la localité par ID
        return view('locality.show', [
            'locality' => $locality, // Passe la localité à la vue
        ]);
    }

    /**
     * Affiche le formulaire d'édition pour une localité spécifique.
     * (À implémenter : charger les données de la localité et les passer au formulaire).
     */
    public function edit(string $id)
    {
        // TODO : retourner la vue 'locality.edit' avec les données de la localité
    }

    /**
     * Met à jour une localité spécifique dans la base de données.
     * (À implémenter : valider les données reçues, modifier la localité et sauvegarder).
     */
    public function update(Request $request, string $id)
    {
        // TODO : valider et mettre à jour la localité
    }

    /**
     * Supprime une localité spécifique de la base de données.
     * (À implémenter : supprimer l’enregistrement identifié par $id).
     */
    public function destroy(string $id)
    {
        // TODO : supprimer la localité
    }
}
