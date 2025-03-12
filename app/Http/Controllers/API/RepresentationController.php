<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Representation;
use Illuminate\Support\Facades\Gate;

class RepresentationController extends Controller
{
    /**
     * Récupérer toutes les représentations
     */
    public function index()
    {
        return response()->json(Representation::with(['show', 'location'])->get(), 200);
    }

    /**
     * Ajouter une nouvelle représentation
     */
    public function store(Request $request)
    {
        if (!Gate::allows('manage-representations')) {
            return response()->json(['message' => 'Accès interdit'], 403);
        }

        $validated = $request->validate([
            'schedule' => 'required|date',
            'show_id' => 'required|integer|exists:shows,id',
            'location_id' => 'required|integer|exists:locations,id'
        ]);

        $representation = Representation::create($validated);
        return response()->json($representation, 201);
    }

    /**
     * Afficher une représentation spécifique
     */
    public function show($id)
    {
        $representation = Representation::with(['show', 'location'])->find($id);
        if (!$representation) {
            return response()->json(['message' => 'Représentation non trouvée'], 404);
        }
        return response()->json($representation, 200);
    }

    /**
     * Modifier une représentation
     */
    public function update(Request $request, $id)
    {
        $representation = Representation::find($id);

        if (!Gate::allows('manage-representations')) {
            return response()->json(['message' => 'Accès interdit'], 403);
        }

        if (!$representation) {
            return response()->json(['message' => 'Représentation non trouvée'], 404);
        }

        $validated = $request->validate([
            'schedule' => 'sometimes|date',
            'show_id' => 'sometimes|integer|exists:shows,id',
            'location_id' => 'sometimes|integer|exists:locations,id'
        ]);

        $representation->update($validated);
        return response()->json($representation, 200);
    }

    /**
     * Supprimer une représentation
     */
    public function destroy($id)
    {
        if (!Gate::allows('manage-representations')) {
            return response()->json(['message' => 'Accès interdit'], 403);
        }

        $representation = Representation::find($id);
        if (!$representation) {
            return response()->json(['message' => 'Représentation non trouvée'], 404);
        }

        $representation->delete();
        return response()->json(['message' => 'Représentation supprimée'], 200);
    }
}
