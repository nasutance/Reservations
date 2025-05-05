<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Show;
use Illuminate\Http\Request;
use App\Models\Tag;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ShowController extends Controller
{
    use AuthorizesRequests;

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
    if ($request->filled('sort')) {
        $sort = $request->query('sort');
        $direction = $request->query('direction', 'asc');
        $query->orderBy($sort, $direction);
    }

    // Filtrer par tag
if ($request->filled('tag')) {
    $query->whereHas('tags', function ($q) use ($request) {
        $q->where('id', $request->tag);
    });
}

if ($request->filled('without_tag')) {
    $query->whereDoesntHave('tags', function ($q) use ($request) {
        $q->where('tags.id', $request->without_tag);
    });
}


    // Charger les représentations
    $query->withCount('representations');

    // Pagination (10 par page par défaut)
    $shows = $query->paginate($request->get('per_page', 10))->withQueryString();

    return Inertia::render('Show/Index', [
        'shows' => $shows,
        'filters' => $request->only([
            'q', 'artist', 'location', 'postal_code',
            'min_duration', 'max_duration', 'sort', 'direction', 'tag'
        ]),
    'tags' => Tag::all(['id', 'tag']),
    ]);
}

public function show(string $id)
{
    $show = Show::with([
        'artistTypes.artist',
        'artistTypes.type',
        'representations.location',
        'location',
        'tags'
    ])->findOrFail($id);

    return Inertia::render('Show/Show', [
        'show' => $show,
        'allTags' => Tag::all(['id', 'tag']),
    ]);
}

public function update(Request $request, Show $show)
{
    $validated = $request->validate([
        'bookable' => 'required|boolean',
        'price_ids' => 'nullable|array',
        'price_ids.*' => 'exists:prices,id',
    ]);

    // Mettre à jour le champ `bookable`
    $show->update([
        'bookable' => $validated['bookable'],
    ]);

    // Synchroniser les prix associés
    $show->prices()->sync($validated['price_ids'] ?? []);

    return redirect()->back()->with('success', 'Spectacle mis à jour.');
}


}
