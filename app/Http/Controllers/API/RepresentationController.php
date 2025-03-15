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
     public function index(Request $request)
     {
         $query = Representation::query();

         // Filtrer par spectacle (`?show=Ayiti`)
         if ($request->has('show')) {
             $showTitle = $request->query('show');
             $query->whereHas('show', function ($q) use ($showTitle) {
                 $q->where('title', 'like', "%$showTitle%");
             });
         }

         // Filtrer par localisation (`?location=Bruxelles`)
         if ($request->has('location')) {
             $locationName = $request->query('location');
             $query->whereHas('location.locality', function ($q) use ($locationName) {
                 $q->where('locality', 'like', "%$locationName%");
             });
         }

         return response()->json($query->with(['show', 'location'])->get(), 200);
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
     public function show(Request $request, $id)
   {
       $representation = Representation::with(['show', 'location'])->find($id);

       if (!$representation) {
           return response()->json(['message' => 'Représentation non trouvée.'], 404);
       }

       // Ajouter un lien vers les réservations sans afficher les détails
       $representation->reservations_links = $representation->reservations->map(function ($reservation) {
           return [
               'id' => $reservation->id,
               'link' => route('reservations.show', ['reservation' => $reservation->id])
           ];
       });

       // Supprimer la relation brute `reservations`
       unset($representation->reservations);

       return response()->json($representation);
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
