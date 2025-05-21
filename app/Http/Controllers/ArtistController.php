<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use App\Models\ArtistType;
use App\Models\Troupe;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Inertia\Inertia;

class ArtistController extends Controller
{
    // Intègre les méthodes pour vérifier les autorisations définies dans les Policies
    use AuthorizesRequests;

    /**
     * Affiche la liste des artistes (avec leurs types et spectacles liés).
     */
    public function index()
    {
        $this->authorize('viewAny', Artist::class);
    
        $artists = Artist::with(['artistTypes.type', 'artistTypes.shows', 'troupe'])
                         ->select('id', 'firstname', 'lastname', 'troupe_id')
                         ->get();
    
        $artistTypes = ArtistType::with(['type', 'shows'])->get();
        $types = \App\Models\Type::select('id', 'type')->get();
        $shows = \App\Models\Show::select('id', 'title')->get();
        $troupes = Troupe::select('id', 'name', 'logo_url')->get(); 
    
        return Inertia::render('Artist/Index', [
            'artists' => $artists,
            'artistTypes' => $artistTypes,
            'types' => $types,
            'shows' => $shows,
            'troupes' => $troupes, 
        ]);
    }
    
    /**
     * Affiche le formulaire de création d’un artiste.
     */
    public function create()
    {
        $this->authorize('create', Artist::class);
        return Inertia::render('Artist/Create');
    }

    /**
     * Traite l'enregistrement d’un nouvel artiste avec ses types et spectacles.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Artist::class);

        // Validation des champs du formulaire
        $validated = $request->validate([
            'firstname' => 'required|max:60',
            'lastname' => 'required|max:60',
            'troupe_id' => 'nullable|exists:troupes,id',
            'types' => 'array', // tableau d’ID de types
            'types.*' => 'exists:types,id',
            'shows' => 'array', // tableau associatif type_id => [show_id,...]
            'shows.*' => 'array',
            'shows.*.*' => 'exists:shows,id',
        ]);

        // Création de l’artiste principal
        $artist = Artist::create([
            'firstname' => $validated['firstname'],
            'lastname' => $validated['lastname'],
            'troupe_id' => $validated['troupe_id'] ?? null,
        ]);

        // Création des types et associations aux spectacles
        foreach ($validated['types'] as $typeId) {
            $artistType = $artist->artistTypes()->create([
                'type_id' => $typeId,
            ]);

            // Récupération des spectacles associés à ce type
            $showsForThisType = $validated['shows'][$typeId] ?? [];
            $artistType->shows()->sync($showsForThisType);
        }

        // Redirection vers le tableau de bord
        return Inertia::location('/dashboard');
    }

    /**
     * Affiche un artiste en détail.
     */
    public function show($id)
    {
        $artist = Artist::with('troupe')->findOrFail($id);
        $this->authorize('view', $artist);

        return Inertia::render('Artist/Show', [
            'artist' => [
                'id' => $artist->id,
                'name' => $artist->name,                
                'troupe' => $artist->troupe ? [
                    'name' => $artist->troupe->name,
                    'logo_url' => $artist->troupe->logo_url,
                ] : null,
            ]
        ]);
        }

    /**
     * Affiche le formulaire de modification d’un artiste.
     */
    public function edit($id)
    {
        $artist = Artist::findOrFail($id);
        $this->authorize('update', $artist);

        return Inertia::render('Artist/Edit', [
            'artist' => $artist
        ]);
    }

    /**
     * Met à jour les données d’un artiste (nom, types, spectacles liés).
     */
    public function update(Request $request, Artist $artist)
    {
        $validated = $request->validate([
            'firstname' => 'required',
            'lastname' => 'required',
            'troupe_id' => 'nullable|exists:troupes,id',
            'types' => 'array',
            'types.*' => 'exists:types,id',
            'shows' => 'array',
            'shows.*' => 'array',
            'shows.*.*' => 'exists:shows,id',
        ]);

        $artist->update([
            'firstname' => $validated['firstname'],
            'lastname' => $validated['lastname'],
            'troupe_id' => $validated['troupe_id'] ?? null,
        ]);
        
        // Supprimer les anciens artistTypes (et leurs liens vers les shows via cascade)
        $artist->artistTypes()->delete();
    
        // Re-mappage avec récupération des nouveaux IDs
        $newArtistTypes = [];
    
        foreach ($validated['types'] as $typeId) {
            $artistType = $artist->artistTypes()->create([
                'type_id' => $typeId,
            ]);
            $newArtistTypes[$typeId] = $artistType->id;
        }
    
        // Synchronisation des shows avec les nouveaux artist_type.id
        foreach ($newArtistTypes as $typeId => $artistTypeId) {
            $showIds = $validated['shows'][$typeId] ?? [];
            ArtistType::find($artistTypeId)?->shows()->sync($showIds);
        }
    
        return Inertia::location('/dashboard');
    }
    

    /**
     * Supprime un artiste de la base de données.
     */
    public function destroy($id)
    {
        $artist = Artist::findOrFail($id);
        $this->authorize('delete', $artist);

        $artist->delete();

        return Inertia::location('/dashboard');
    }
}
