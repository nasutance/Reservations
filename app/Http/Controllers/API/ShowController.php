<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Show;
use Illuminate\Support\Facades\Gate;

class ShowController extends Controller
{
    /**
     * Récupérer tous les spectacles (avec relations)
     */
    public function index()
    {
        return response()->json(Show::with(['location', 'representations', 'reviews', 'artistTypes', 'prices'])->get(), 200);
    }

    /**
     * Ajouter un nouveau spectacle
     */
    public function store(Request $request)
    {

      if (!Gate::allows('manage-shows')) {
        return response()->json(['message' => 'Accès interdit'], 403);
      }

        $validated = $request->validate([
            'slug' => 'required|string|unique:shows,slug|max:255',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'poster_url' => 'nullable|url',
            'duration' => 'required|integer|min:1',
            'created_in' => 'required|integer|min:1800|max:' . date('Y'),
            'location_id' => 'required|integer|max:10',
            'bookable' => 'required|boolean'
        ]);

        $show = Show::create($validated);
        return response()->json($show, 201);
    }

    /**
     * Afficher un spectacle spécifique
     */
    public function show($id)
    {
        $show = Show::with(['location', 'representations', 'reviews', 'artistTypes', 'prices'])->find($id);
        if (!$show) {
            return response()->json(['message' => 'Spectacle non trouvé'], 404);
        }
        return response()->json($show, 200);
    }

    /**
     * Modifier un spectacle
     */
    public function update(Request $request, $id)
    {
        $show = Show::find($id);

        if (!Gate::allows('manage-shows')) {
            return response()->json(['message' => 'Accès interdit'], 403);
        }

        if (!$show) {
            return response()->json(['message' => 'Spectacle non trouvé'], 404);
        }

        $validated = $request->validate([
            'slug' => 'sometimes|string|unique:shows,slug,' . $id . '|max:255',
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'poster_url' => 'nullable|url',
            'duration' => 'sometimes|integer|min:1',
            'created_in' => 'sometimes|integer|min:1800|max:' . date('Y'),
            'location_id' => 'sometimes|integer|max:10',
            'bookable' => 'sometimes|boolean'
        ]);

        $show->update($validated);
        return response()->json($show, 200);
    }

    /**
     * Supprimer un spectacle
     */
    public function destroy($id)
    {

      if (!Gate::allows('manage-shows')) {
        return response()->json(['message' => 'Accès interdit'], 403);
      }

        $show = Show::find($id);
        if (!$show) {
            return response()->json(['message' => 'Spectacle non trouvé'], 404);
        }

        $show->delete();
        return response()->json(['message' => 'Spectacle supprimé'], 200);
    }
}
