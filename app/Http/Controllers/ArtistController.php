<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use App\Models\ArtistType;
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
        // Vérifie si l'utilisateur a le droit de voir tous les artistes
        $this->authorize('viewAny', Artist::class);

        // Récupère tous les artistes avec leurs types et spectacles associés
        $artists = Artist::with(['artistTypes.type', 'artistTypes.shows'])
                         ->select('id', 'firstname', 'lastname')
                         ->get();

        // Affiche la vue Inertia Artist/Index avec les données des artistes
        return Inertia::render('Artist/Index', [
            'artists' => $artists
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
        $artist = Artist::findOrFail($id);
        $this->authorize('view', $artist);

        return Inertia::render('Artist/Show', [
            'artist' => $artist
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
            'types' => 'array',
            'types.*' => 'exists:types,id',
            'shows' => 'array',
            'shows.*' => 'array',
            'shows.*.*' => 'exists:shows,id',
        ]);

        $artist->update([
            'firstname' => $validated['firstname'],
            'lastname' => $validated['lastname'],
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
