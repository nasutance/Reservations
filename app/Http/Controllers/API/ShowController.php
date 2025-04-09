<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Show;
use Illuminate\Support\Facades\Gate;

class ShowController extends Controller
{

  public function index(Request $request)
  {
      $query = Show::query();

      // Filtre par mot-clé (titre ou description)
      if ($request->filled('q')) {
          $query->where(function($q) use ($request) {
              $q->where('title', 'like', '%' . $request->q . '%')
                ->orWhere('description', 'like', '%' . $request->q . '%');
          });
      }

      // Filtrer par artiste
      if ($request->has('artist')) {
          $artistName = $request->query('artist');
          $query->whereHas('artistTypes.artist', function ($q) use ($artistName) {
              $q->whereRaw("CONCAT(firstname, ' ', lastname) LIKE ?", ["%$artistName%"]);
          });
      }

      // Filtrer par ville
      if ($request->has('location')) {
          $locationName = $request->query('location');
          $query->whereHas('location.locality', function ($q) use ($locationName) {
              $q->where('locality', 'like', "%$locationName%");
          });
      }

      // Filtrer par code postal
      if ($request->filled('postal_code')) {
          $query->whereHas('representations.location', function ($q) use ($request) {
              $q->where('locality_postal_code', $request->postal_code);
          });
      }

      // Durée min / max
      if ($request->filled('min_duration')) {
          $query->where('duration', '>=', $request->min_duration);
      }

      if ($request->filled('max_duration')) {
          $query->where('duration', '<=', $request->max_duration);
      }

      // Tri dynamique
      if ($request->has('sort')) {
          $sort = $request->query('sort');
          $direction = $request->query('direction', 'asc');
          $query->orderBy($sort, $direction);
      }

      // Pagination (par défaut 10 résultats par page)
      $shows = $query->paginate($request->get('per_page', 10));

      return response()->json($shows);
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
            'location_id' => 'required|exists:locations,id',
            'bookable' => 'required|boolean'
        ]);

        $show = Show::create($validated);
        return response()->json($show, 201);
    }

    /**
     * Afficher un spectacle spécifique
     */
     public function show(Request $request, $id)
     {
         $relations = explode(',', $request->query('include', 'representations,prices'));

         // Vérifier si l'utilisateur demande aussi les artistes et types
         $includeArtistTypes = in_array('artistTypes', $relations);

         $show = Show::with($relations)->find($id);

         if (!$show) {
             return response()->json(['message' => 'Spectacle non trouvé'], 404);
         }

         // Charger les artistes et types associés au spectacle si demandé
         if ($includeArtistTypes) {
             $show->load(['artistTypes.artist:id,firstname,lastname', 'artistTypes.type:id,type']);

             // Transformer la structure pour ne garder que l'essentiel
             $show->artists = $show->artistTypes->map(function ($artistType) {
                 return [
                     'id' => $artistType->artist->id,
                     'firstname' => $artistType->artist->firstname,
                     'lastname' => $artistType->artist->lastname,
                     'type' => [
                         'id' => $artistType->type->id,
                         'name' => $artistType->type->type,
                     ]
                 ];
             });

             // Supprimer la relation intermédiaire `artistTypes` du JSON
             unset($show->artistTypes);
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
            'location_id' => 'sometimes|integer|exists:locations,id',
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
