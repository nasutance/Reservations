<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Artist;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ArtistController extends Controller
{
    use AuthorizesRequests; // Active la gestion des autorisations via les policies Laravel

    /**
     * Affiche la liste des artistes (GET /api/artists)
     */
    public function index()
    {
        $this->authorize('viewAny', Artist::class); // Vérifie si l'utilisateur peut voir la liste des artistes

        // Récupère tous les artistes et applique un formatage avec liens HATEOAS
        $artists = Artist::all()->map(function ($artist) {
            return $this->formatArtistWithLinks($artist);
        });

        // Retourne la réponse JSON avec encodage UTF-8 préservé
        return response()->json($artists, 200, [], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Crée un nouvel artiste (POST /api/artists)
     */
    public function store(Request $request)
    {
        $this->authorize('create', Artist::class); // Vérifie si l'utilisateur peut créer un artiste

        // Valide les données reçues
        $validated = $request->validate([
            'firstname' => 'required|max:60',
            'lastname' => 'required|max:60',
        ]);

        // Crée l'artiste avec les données validées
        $artist = Artist::create($validated);

        // Retourne l'artiste nouvellement créé, avec code 201 (ressource créée)
        return response()->json($this->formatArtistWithLinks($artist), 201);
    }

    /**
     * Affiche un artiste spécifique (GET /api/artists/{id})
     */
    public function show($id)
    {
        $artist = Artist::find($id); // Recherche l'artiste par son ID

        if (!$artist) {
            return response()->json(['message' => 'Artist not found'], 404); // Erreur si introuvable
        }

        $this->authorize('view', $artist); // Vérifie si l'utilisateur peut voir cet artiste

        return response()->json($this->formatArtistWithLinks($artist), 200);
    }

    /**
     * Met à jour un artiste (PUT /api/artists/{id})
     */
    public function update(Request $request, $id)
    {
        $artist = Artist::find($id); // Recherche l'artiste

        if (!$artist) {
            return response()->json(['message' => 'Artist not found'], 404);
        }

        $this->authorize('update', $artist); // Vérifie l'autorisation

        // Valide les champs fournis uniquement (optionnels)
        $validated = $request->validate([
            'firstname' => 'sometimes|string|max:60',
            'lastname' => 'sometimes|string|max:60',
        ]);

        // Met à jour l'artiste
        $artist->update($validated);

        return response()->json($this->formatArtistWithLinks($artist), 200);
    }

    /**
     * Supprime un artiste (DELETE /api/artists/{id})
     */
    public function destroy($id)
    {
        $artist = Artist::find($id);

        if (!$artist) {
            return response()->json(['message' => 'Artist not found'], 404);
        }

        $this->authorize('delete', $artist); // Vérifie l'autorisation

        $artist->delete(); // Supprime l'artiste

        return response()->json(['message' => 'Artist deleted'], 200);
    }

    /**
     * Formate un artiste avec des liens HATEOAS
     *
     * Cette méthode ajoute à chaque artiste des liens d'action (afficher, modifier, supprimer).
     * Elle favorise une meilleure navigation pour les clients d’API REST.
     */
    private function formatArtistWithLinks(Artist $artist)
    {
        return [
            'id' => $artist->id,
            'firstname' => $artist->firstname,
            'lastname' => $artist->lastname,
            '_links' => [
                'self' => [
                    'href' => route('artists.show', ['artist' => $artist->id]),
                ],
                'update' => [
                    'href' => route('artists.update', ['artist' => $artist->id]),
                    'method' => 'PUT',
                ],
                'delete' => [
                    'href' => route('artists.destroy', ['artist' => $artist->id]),
                    'method' => 'DELETE',
                ],
            ],
        ];
    }
}
