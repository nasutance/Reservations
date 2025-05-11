<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Price;
use Inertia\Inertia;

class PriceController extends Controller
{
    /**
     * Affiche la liste de tous les tarifs dans le tableau de bord admin.
     * Utilise Inertia pour rendre une vue Vue.js (`AdminDashboard`) avec les données.
     */
    public function index()
    {
        return Inertia::render('AdminDashboard', [
            'prices' => Price::all(), // Envoie tous les tarifs au composant Vue
            // autres propriétés éventuelles : messages flash, utilisateur, etc.
        ]);
    }

    /**
     * Affiche le formulaire de création d'un nouveau tarif.
     * (À implémenter si nécessaire, par exemple avec une page Vue via Inertia).
     */
    public function create()
    {
        // TODO : retourner un composant Vue avec un formulaire (ex: PriceForm.vue)
    }

    /**
     * Enregistre un nouveau tarif dans la base de données.
     * Valide les données du formulaire, puis crée un enregistrement `Price`.
     */
    public function store(Request $request)
    {
        // Validation des données envoyées par le formulaire
        $validated = $request->validate([
            'type' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        // Création du tarif avec les données validées
        Price::create($validated);

        // Redirection vers le dashboard (comportement SPA via Inertia)
        return Inertia::location('/dashboard');
    }

    /**
     * Affiche les détails d’un tarif spécifique.
     * Cette méthode n’utilise pas Inertia ici, mais une vue Blade classique.
     */
    public function show(string $id)
    {
        $price = Price::find($id); // Recherche du tarif par ID

        return view('price.show', [ // Retour d'une vue Blade
            'price' => $price,
        ]);
    }

    /**
     * Affiche le formulaire de modification pour un tarif spécifique.
     * (À implémenter si nécessaire avec un composant Vue via Inertia).
     */
    public function edit(string $id)
    {
        // TODO : retourner un composant Vue avec les données du tarif à modifier
    }

    /**
     * Met à jour un tarif existant dans la base de données.
     * Valide les nouvelles données puis applique la mise à jour.
     */
    public function update(Request $request, Price $price)
    {
        // Validation des champs
        $validated = $request->validate([
            'type' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        // Mise à jour de l'objet Price
        $price->update($validated);

        // Redirection vers le dashboard
        return Inertia::location('/dashboard');
    }

    /**
     * Supprime un tarif de la base de données.
     */
    public function destroy(Price $price)
    {
        $price->delete(); // Suppression du tarif

        return Inertia::location('/dashboard'); // Redirection vers le dashboard
    }
}
