<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Artist;

class ArtistController extends Controller
{
    /**
     * Display a listing of the resource.
     */
     public function index()
     {
     $artists = Artist::all();
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

         return response()->json($artist, 201);
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

         return response()->json($artist, 200);
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

         return response()->json($artist, 200);
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

}
