<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Show;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ShowController extends Controller
{
    use AuthorizesRequests; // Active la gestion des autorisations via les policies Laravel

    /**
     * Affiche une liste paginée des spectacles avec filtres (GET /api/shows)
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Show::class); // Vérifie l'autorisation globale sur les spectacles

        $query = Show::query(); // Démarre une requête Eloquent

        // 🔍 Filtrage par mot-clé sur le titre ou la description
        if ($request->filled('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->q . '%')
                  ->orWhere('description', 'like', '%' . $request->q . '%');
            });
        }

        // 🔍 Filtrage par nom d’artiste (prénom + nom concaténés)
        if ($request->has('artist')) {
            $artistName = $request->query('artist');
            $query->whereHas('artistTypes.artist', function ($q) use ($artistName) {
                $q->whereRaw("CONCAT(firstname, ' ', lastname) LIKE ?", ["%$artistName%"]);
            });
        }

        // 🔍 Filtrage par nom de localité
        if ($request->has('location')) {
            $locationName = $request->query('location');
            $query->whereHas('location.locality', function ($q) use ($locationName) {
                $q->where('locality', 'like', "%$locationName%");
            });
        }

        // 🔍 Filtrage par code postal
        if ($request->filled('postal_code')) {
            $query->whereHas('representations.location', function ($q) use ($request) {
                $q->where('locality_postal_code', $request->postal_code);
            });
        }

        // 🔍 Filtrage par durée minimale
        if ($request->filled('min_duration')) {
            $query->where('duration', '>=', $request->min_duration);
        }

        // 🔍 Filtrage par durée maximale
        if ($request->filled('max_duration')) {
            $query->where('duration', '<=', $request->max_duration);
        }

        // ↕️ Tri dynamique
        if ($request->has('sort')) {
            $query->orderBy($request->query('sort'), $request->query('direction', 'asc'));
        }

        // 📄 Résultat paginé (10 résultats par défaut)
        return response()->json($query->paginate($request->get('per_page', 10)));
    }

    /**
     * Enregistre un nouveau spectacle (POST /api/shows)
     */
    public function store(Request $request)
    {
        $this->authorize('create', Show::class); // Vérifie les droits

        // Validation des champs requis
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

        // Création de l'entité spectacle
        $show = Show::create($validated);
        return response()->json($show, 201);
    }

    /**
     * Affiche un spectacle spécifique avec ses relations (GET /api/shows/{id})
     */
    public function show(Request $request, $id)
    {
        // Chargement conditionnel des relations (par défaut : representations, prices)
        $show = Show::with(explode(',', $request->query('include', 'representations,prices')))->find($id);
        if (!$show) return response()->json(['message' => 'Spectacle non trouvé'], 404);

        $this->authorize('view', $show); // Vérifie les droits sur ce spectacle

        // Si l'utilisateur veut inclure les artistes avec leurs types
        if (in_array('artistTypes', explode(',', $request->query('include', '')))) {
            $show->load(['artistTypes.artist:id,firstname,lastname', 'artistTypes.type:id,type']);

            // On reformate les données pour une réponse plus lisible
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

            unset($show->artistTypes); // Nettoyage pour ne pas dupliquer les données
        }

        return response()->json($show, 200);
    }

    /**
     * Met à jour un spectacle existant (PUT /api/shows/{id})
     */
    public function update(Request $request, $id)
    {
        $show = Show::find($id);
        if (!$show) return response()->json(['message' => 'Spectacle non trouvé'], 404);

        $this->authorize('update', $show); // Vérifie les droits

        // Validation souple (champs optionnels)
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

        // Mise à jour
        $show->update($validated);
        return response()->json($show, 200);
    }

    /**
     * Supprime un spectacle (DELETE /api/shows/{id})
     */
    public function destroy($id)
    {
        $show = Show::find($id);
        if (!$show) return response()->json(['message' => 'Spectacle non trouvé'], 404);

        $this->authorize('delete', $show); // Vérifie les droits

        $show->delete();
        return response()->json(['message' => 'Spectacle supprimé'], 200);
    }
}
