<?php

namespace App\Http\Controllers;

use App\Models\Price;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PriceController extends Controller
{
    use AuthorizesRequests;

    /**
     * Enregistre un nouveau tarif dans la base de données.
     * Valide les données du formulaire, puis crée un enregistrement `Price`.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Price::class);

        // Validation des données envoyées par le formulaire
        $validated = $request->validate([
            'type' => 'required|string|max:30',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        // Création du tarif avec les données validées
        Price::create($validated);

        // Redirection vers le dashboard (comportement SPA via Inertia)
        return Inertia::location('/dashboard');
    }

    /**
     * Met à jour un tarif existant dans la base de données.
     * Valide les nouvelles données puis applique la mise à jour.
     */
    public function update(Request $request, Price $price)
    {
        $this->authorize('update', $price);

        // Validation des champs
        $validated = $request->validate([
            'type' => 'required|string|max:30',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'start_date' => 'required|date',
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
        $this->authorize('delete', $price);

        $price->delete(); // Suppression du tarif

        return Inertia::location('/dashboard'); // Redirection vers le dashboard
    }
}
