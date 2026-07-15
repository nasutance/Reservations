<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use App\Models\ArtistType;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ArtistController extends Controller
{
    // Intègre les méthodes pour vérifier les autorisations définies dans les Policies
    use AuthorizesRequests;

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
     * Met à jour les données d’un artiste (nom, types, spectacles liés).
     */
    public function update(Request $request, Artist $artist)
    {
        $this->authorize('update', $artist);

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
