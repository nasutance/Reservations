<?php

namespace App\Http\Controllers;

use App\Models\Representation;
use App\Models\Show;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class RepresentationController extends Controller
{
    use AuthorizesRequests; // Mixin Laravel pour activer la gestion des autorisations (policies)

    /**
     * Affiche la liste des représentations dans le tableau de bord.
     * Utilise les relations Eloquent pour inclure les données du spectacle et du lieu.
     * Vérifie que l'utilisateur a le droit de consulter les représentations.
     */
    public function index()
    {
        $this->authorize('viewAny', Representation::class); // Vérifie les droits (policy)

        return Inertia::render('Dashboard/Dashboard', [
            'representations' => Representation::with(['show', 'location'])->get(), // Chargement eager
            'shows' => Show::all(),           // Liste des spectacles pour formulaires
            'locations' => Location::all(),   // Liste des lieux pour formulaires
        ]);
    }

    /**
     * Enregistre une nouvelle représentation en base de données.
     * Valide les données du formulaire (planning, spectacle, lieu).
     * Vérifie l'autorisation de création via policy.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Representation::class); // Vérifie les droits

        $validated = $request->validate([
            'schedule' => 'required|date',              // Date/heure au format valide
            'show_id' => 'required|exists:shows,id',    // FK existante dans `shows`
            'location_id' => 'required|exists:locations,id', // FK existante dans `locations`
        ]);

        // Création de la représentation
        $representation = Representation::create($validated);

        // Redirige vers le dashboard (full reload côté client avec Inertia::location)
        return Inertia::location('/dashboard');
    }

    /**
     * Met à jour une représentation existante.
     * Vérifie que la ressource existe et que l'utilisateur a le droit de la modifier.
     */
    public function update(Request $request, $id)
    {
        $representation = Representation::findOrFail($id); // 404 si non trouvé

        $this->authorize('update', $representation); // Vérifie les droits

        $validated = $request->validate([
            'schedule' => 'required|date',
            'show_id' => 'required|exists:shows,id',
            'location_id' => 'required|exists:locations,id',
        ]);

        $representation->update($validated); // Sauvegarde

        return Inertia::location('/dashboard'); // Redirection vers la liste
    }

    /**
     * Supprime une représentation de la base de données.
     * Vérifie d’abord que la ressource existe et que l'utilisateur a le droit de la supprimer.
     */
    public function destroy($id)
    {
        $representation = Representation::findOrFail($id);

        $this->authorize('delete', $representation); // Vérifie les droits

        $representation->delete(); // Suppression

        return Inertia::location('/dashboard'); // Redirection
    }
}
