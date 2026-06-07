<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreRepresentationRequest;
use App\Http\Requests\Api\UpdateRepresentationRequest;
use App\Http\Resources\RepresentationResource;
use Illuminate\Http\Request;
use App\Models\Representation;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class RepresentationController extends Controller
{
    use AuthorizesRequests; // Permet l’utilisation des policies pour sécuriser les actions

    /**
     * Liste les représentations avec filtres facultatifs (GET /api/representations)
     */
    public function index(Request $request)
    {
        // Démarre une requête Eloquent sur le modèle Representation
        $query = Representation::query();

        // Filtre par titre du spectacle si le paramètre 'show' est présent dans l’URL
        if ($request->has('show')) {
            $showTitle = $request->query('show');
            $query->whereHas('show', fn($q) => $q->where('title', 'like', "%$showTitle%"));
        }

        // Filtre par nom de localité si le paramètre 'location' est fourni
        if ($request->has('location')) {
            $locationName = $request->query('location');
            $query->whereHas('location.locality', fn($q) => $q->where('locality', 'like', "%$locationName%"));
        }

        // Retourne les représentations avec leurs relations 'show' et 'location'
        return RepresentationResource::collection($query->with(['show', 'location'])->get());
    }

    /**
     * Enregistre une nouvelle représentation (POST /api/representations)
     */
    public function store(StoreRepresentationRequest $request)
    {
        $representation = Representation::create($request->validated());

        return (new RepresentationResource($representation))->response()->setStatusCode(201);
    }

    /**
     * Affiche une représentation spécifique avec liens vers ses réservations (GET /api/representations/{id})
     */
    public function show(Request $request, $id)
    {
        // Recherche la représentation avec ses relations associées
        $representation = Representation::with(['show', 'location', 'reservations'])->find($id);

        if (!$representation) {
            return response()->json(['message' => 'Représentation non trouvée.'], 404);
        }

        // Génère une liste de liens vers les réservations associées (style HATEOAS)
        $representation->reservations_links = $representation->reservations->map(fn($reservation) => [
            'id' => $reservation->id,
            'link' => route('reservations.show', ['reservation' => $reservation->id]),
        ]);

        // Supprime la liste brute des réservations de l’objet retourné
        unset($representation->reservations);

        // Retourne les données enrichies
        return response()->json($representation);
    }

    /**
     * Met à jour une représentation (PUT /api/representations/{id})
     */
    public function update(UpdateRepresentationRequest $request, $id)
    {
        $representation = Representation::find($id);

        if (!$representation) {
            return response()->json(['message' => 'Représentation non trouvée'], 404);
        }

        $this->authorize('update', $representation);

        $representation->update($request->validated());

        return response()->json($representation, 200);
    }

    /**
     * Supprime une représentation (DELETE /api/representations/{id})
     */
    public function destroy($id)
    {
        $representation = Representation::find($id);

        if (!$representation) {
            return response()->json(['message' => 'Représentation non trouvée'], 404);
        }

        $this->authorize('delete', $representation); // Vérifie l'autorisation

        $representation->delete();

        return response()->json(['message' => 'Représentation supprimée'], 200);
    }
}
