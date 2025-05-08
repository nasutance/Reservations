<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Artist;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ArtistController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', Artist::class);

        $artists = Artist::all()->map(function ($artist) {
            return $this->formatArtistWithLinks($artist);
        });

        return response()->json($artists, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Artist::class);

        $validated = $request->validate([
            'firstname' => 'required|max:60',
            'lastname' => 'required|max:60',
        ]);

        $artist = Artist::create($validated);

        return response()->json($this->formatArtistWithLinks($artist), 201);
    }

    public function show($id)
    {
        $artist = Artist::find($id);

        if (!$artist) {
            return response()->json(['message' => 'Artist not found'], 404);
        }

        $this->authorize('view', $artist);

        return response()->json($this->formatArtistWithLinks($artist), 200);
    }

    public function update(Request $request, $id)
    {
        $artist = Artist::find($id);

        if (!$artist) {
            return response()->json(['message' => 'Artist not found'], 404);
        }

        $this->authorize('update', $artist);

        $validated = $request->validate([
            'firstname' => 'sometimes|string|max:60',
            'lastname' => 'sometimes|string|max:60',
        ]);

        $artist->update($validated);

        return response()->json($this->formatArtistWithLinks($artist), 200);
    }

    public function destroy($id)
    {
        $artist = Artist::find($id);

        if (!$artist) {
            return response()->json(['message' => 'Artist not found'], 404);
        }

        $this->authorize('delete', $artist);

        $artist->delete();

        return response()->json(['message' => 'Artist deleted'], 200);
    }

     // Méthode pour formater un artiste avec des liens hypermédia
     // Facilite la découverte des actions disponibles sur une ressource.
     // Permet aux clients de naviguer dans l’API dynamiquement sans devoir coder en dur les URLs.
     // Encourage une approche RESTful plus complète.

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