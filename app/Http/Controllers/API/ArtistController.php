<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Artist;
use Illuminate\Support\Facades\Gate;

class ArtistController extends Controller
{
    /**
     * Display a listing of the resource.
     */
     public function index()
     {
     $artists = Artist::all()->map(function ($artist) {
       return $this->formatArtistWithLinks($artist);
     });

     return response()->json($artists, 200, [], JSON_UNESCAPED_UNICODE);
     }

    /**
     * Store a newly created resource in storage.
     */
     public function store(Request $request)
     {
       if (!Gate::allows('update-artist')) {
         return response()->json(["error"=>"Action non autorisée."],403);
       }

         $validated = $request->validate([
             'firstname' => 'required|max:60',
             'lastname' => 'required|max:60',
         ]);

         $artist = Artist::create($validated);

         return response()->json($this->formatArtistWithLinks($artist), 201);
       }


    /**
     * Display the specified resource.
     */
     public function show($id)
     {
         $artist = Artist::find($id);

         if (!$artist) {
             return response()->json(['message' => 'Artist not found'], 404);
         }
         return response()->json($this->formatArtistWithLinks($artist), 200);
       }

    /**
     * Update the specified resource in storage.
     */
     public function update(Request $request, $id)
     {
       if (!Gate::allows('update-artist')) {
         return response()->json(["error"=>"Action non autorisée."],403);
       }
         $artist = Artist::find($id);

         if (!$artist) {
             return response()->json(['message' => 'Artist not found'], 404);
         }

         $validated = $request->validate([
             'firstname' => 'sometimes|string|max:60',
             'lastname' => 'sometimes|string|max:60',
         ]);

         $artist->update($validated);

         return response()->json($this->formatArtistWithLinks($artist), 200);
       }


    /**
     * Remove the specified resource from storage.
     */
     public function destroy($id)
     {
       if (!Gate::allows('delete-artist')) {
         return response()->json(["error"=>"Action non autorisée."],403);
       }
         $artist = Artist::find($id);

         if (!$artist) {
             return response()->json(['message' => 'Artist not found'], 404);
         }

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
         '_links' => [ // Tableau de liens HATEOAS
           'self' => [ // Donne l’URL permettant de récupérer les détails de cet artiste (GET /artists/{id})
             'href' => route('artists.show', ['artist' => $artist->id]),
           ],
           'update' => [ // Fournit l’URL pour mettre à jour cet artiste (PUT /artists/{id})
             'href' => route('artists.update', ['artist' => $artist->id]),
             'method' => 'PUT',
           ],
           'delete' => [ // Fournit l’URL pour supprimer cet artiste (DELETE /artists/{id})
             'href' => route('artists.destroy', ['artist' => $artist->id]),
             'method' => 'DELETE',
           ],
         ],
       ];
     }
}
