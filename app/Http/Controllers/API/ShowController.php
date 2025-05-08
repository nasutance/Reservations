<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Show;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ShowController extends Controller
{
    use AuthorizesRequests;
    
    public function index(Request $request)
    {
        $this->authorize('viewAny', Show::class); // ✅ auth via Policy

        $query = Show::query();

        if ($request->filled('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->q . '%')
                    ->orWhere('description', 'like', '%' . $request->q . '%');
            });
        }

        if ($request->has('artist')) {
            $artistName = $request->query('artist');
            $query->whereHas('artistTypes.artist', function ($q) use ($artistName) {
                $q->whereRaw("CONCAT(firstname, ' ', lastname) LIKE ?", ["%$artistName%"]);
            });
        }

        if ($request->has('location')) {
            $locationName = $request->query('location');
            $query->whereHas('location.locality', function ($q) use ($locationName) {
                $q->where('locality', 'like', "%$locationName%");
            });
        }

        if ($request->filled('postal_code')) {
            $query->whereHas('representations.location', function ($q) use ($request) {
                $q->where('locality_postal_code', $request->postal_code);
            });
        }

        if ($request->filled('min_duration')) {
            $query->where('duration', '>=', $request->min_duration);
        }

        if ($request->filled('max_duration')) {
            $query->where('duration', '<=', $request->max_duration);
        }

        if ($request->has('sort')) {
            $query->orderBy($request->query('sort'), $request->query('direction', 'asc'));
        }

        return response()->json($query->paginate($request->get('per_page', 10)));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Show::class); // ✅ auth via Policy

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

    public function show(Request $request, $id)
    {
        $show = Show::with(explode(',', $request->query('include', 'representations,prices')))->find($id);
        if (!$show) return response()->json(['message' => 'Spectacle non trouvé'], 404);

        $this->authorize('view', $show); // ✅ auth via Policy

        if (in_array('artistTypes', explode(',', $request->query('include', '')))) {
            $show->load(['artistTypes.artist:id,firstname,lastname', 'artistTypes.type:id,type']);
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
            unset($show->artistTypes);
        }

        return response()->json($show, 200);
    }

    public function update(Request $request, $id)
    {
        $show = Show::find($id);
        if (!$show) return response()->json(['message' => 'Spectacle non trouvé'], 404);

        $this->authorize('update', $show); // ✅ auth via Policy

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

    public function destroy($id)
    {
        $show = Show::find($id);
        if (!$show) return response()->json(['message' => 'Spectacle non trouvé'], 404);

        $this->authorize('delete', $show); // ✅ auth via Policy

        $show->delete();
        return response()->json(['message' => 'Spectacle supprimé'], 200);
    }
}
