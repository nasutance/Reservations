<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Show;
use Illuminate\Http\Request;
use App\Models\Tag;
use Illuminate\Support\Facades\Auth;

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
  ])->find($id);

  if (!$show) {
      abort(404, 'Spectacle non trouvé.');
  }

  // Préparer les collaborateurs regroupés par type
  $collaborateurs = [
      'auteur' => [],
      'scénographe' => [],
      'comédien' => [],
  ];

  foreach ($show->artistTypes as $at) {
      $type = $at->type->type ?? 'autre';
      if (isset($collaborateurs[$type])) {
          $collaborateurs[$type][] = $at->artist;
      }
  }

  return Inertia::render('Show/Show', [
      'show' => $show,
      'collaborateurs' => $collaborateurs,
      'auth' => [
          'user' => Auth::user(),
      ],
      'allTags' => Tag::all(['id', 'tag']),
  ]);
}

public function withoutTag(Tag $tag)
{
    $shows = Show::whereDoesntHave('tags', function ($q) use ($tag) {
        $q->where('tags.id', $tag->id);
    })->withCount('representations')->paginate(10);

    return Inertia::render('Show/Index', [
        'shows' => $shows,
        'filters' => [
            'without_tag' => $tag->id,
        ],
        'message' => "Spectacles **sans** le mot-clé « {$tag->tag} »",
        'tags' => Tag::all(['id', 'tag']),
    ]);
}


}
