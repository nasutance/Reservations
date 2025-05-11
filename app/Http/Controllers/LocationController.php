<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Location;

class LocationController extends Controller
{
    /**
     * Affiche la liste de toutes les localisations (salles, lieux...).
     * Récupère toutes les entrées de la table `locations` et les passe à la vue `location.index`.
     */
    public function index()
    {
        return view('location.index', [
            'locations' => Location::all(), // Récupère toutes les localisations
        ]);
    }

    /**
     * Affiche les détails d’une localisation spécifique.
     * Recherche par son identifiant et affiche la vue `location.show`.
     */
    public function show(string $id)
    {
        return view('location.show', [
            'location' => Location::find($id), // Récupère une localisation par ID
        ]);
    }

    // À implémenter :

    /**
     * Affiche le formulaire de création d'une nouvelle localisation.
     */
    public function create()
    {
        // TODO : retourner la vue 'location.create' contenant le formulaire
    }

    /**
     * Enregistre une nouvelle localisation dans la base de données.
     * - Valider les données reçues
     * - Créer une nouvelle instance de Location
     * - Enregistrer en BD
     * - Rediriger avec message de confirmation
     */
    public function store(Request $request)
    {
        // TODO
        // $request->validate([...]);
        // Location::create([...]);
        // return redirect()->route('location.index')->with('success', 'Lieu ajouté');
    }

    /**
     * Affiche le formulaire d'édition d'une localisation.
     */
    public function edit(string $id)
    {
        // TODO : retourner la vue 'location.edit' avec les données du lieu à modifier
    }

    /**
     * Met à jour une localisation existante avec de nouvelles données.
     */
    public function update(Request $request, string $id)
    {
        // TODO : valider les données, mettre à jour l'enregistrement existant
    }

    /**
     * Supprime une localisation spécifique.
     */
    public function destroy(string $id)
    {
        // TODO : supprimer l'enregistrement et rediriger avec un message de confirmation
    }
}
