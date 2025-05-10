<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use App\Models\ArtistType;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Inertia\Inertia;

class ArtistController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', Artist::class);

        $artists = Artist::with(['artistTypes.type', 'artistTypes.shows'])
                         ->select('id', 'firstname', 'lastname')
                         ->get();

        return Inertia::render('Artist/Index', [
            'artists' => $artists
        ]);
    }

    public function create()
    {
        $this->authorize('create', Artist::class);
        return Inertia::render('Artist/Create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Artist::class);
    
        $validated = $request->validate([
            'firstname' => 'required|max:60',
            'lastname' => 'required|max:60',
            'types' => 'array',
            'types.*' => 'exists:types,id',
            'shows' => 'array',
            'shows.*' => 'array',
            'shows.*.*' => 'exists:shows,id',
        ]);
    
        $artist = Artist::create([
            'firstname' => $validated['firstname'],
            'lastname' => $validated['lastname'],
        ]);
    
        foreach ($validated['types'] as $typeId) {
            $artistType = $artist->artistTypes()->create([
                'type_id' => $typeId,
            ]);
    
            $showsForThisType = $validated['shows'][$typeId] ?? [];
            $artistType->shows()->sync($showsForThisType);
        }
    
        return Inertia::location('/dashboard');
    }
    

    public function show($id)
    {
        $artist = Artist::findOrFail($id);
        $this->authorize('view', $artist);

        return Inertia::render('Artist/Show', [
            'artist' => $artist
        ]);
    }

    public function edit($id)
    {
        $artist = Artist::findOrFail($id);
        $this->authorize('update', $artist);

        return Inertia::render('Artist/Edit', [
            'artist' => $artist
        ]);
    }

    public function update(Request $request, Artist $artist)
  {
      $validated = $request->validate([
          'firstname' => 'required',
          'lastname' => 'required',
          'types' => 'array',
          'types.*' => 'exists:types,id',
          'shows' => 'array',
      ]);

      $artist->update($validated);

      // Detach & re-sync artist_type
      $artist->artistTypes()->delete();

      foreach ($validated['types'] as $typeId) {
          $artistType = $artist->artistTypes()->create([
              'type_id' => $typeId,
          ]);

          // shows par type
          $showsForThisType = $validated['shows'][$typeId] ?? [];
          $artistType->shows()->sync($showsForThisType);
      }

      return Inertia::location('/dashboard');

}


    public function destroy($id)
    {
        $artist = Artist::findOrFail($id);
        $this->authorize('delete', $artist);

        $artist->delete();

        return Inertia::location('/dashboard');
    }
}
