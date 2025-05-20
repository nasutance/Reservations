<?php

namespace App\Http\Controllers;

use App\Models\Show;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TagController extends Controller
{
    use AuthorizesRequests;
    /**
     * Attache un mot-clé (tag) à un spectacle (show)
     *
     * Cette méthode est déclenchée lorsqu’un utilisateur (ayant l’autorisation)
     * souhaite ajouter un tag à un spectacle spécifique. Cela permet de gérer
     * la relation many-to-many entre les spectacles et les mots-clés.
     */
    public function attach(Request $request, Show $show)
    {
        // Vérifie que l’utilisateur a le droit d’ajouter un tag à ce spectacle
        $this->authorize('addTag', $show);

        // Valide que l’ID de tag est bien présent et correspond à un tag existant
        $request->validate([
            'tag_id' => 'required|exists:tags,id',
        ]);

        // Évite les doublons en vérifiant si le tag est déjà lié au spectacle
        if (!$show->tags->contains($request->tag_id)) {
            $show->tags()->attach($request->tag_id); // Ajoute le tag via la table pivot
        }

        // Redirection vers la page précédente avec un message de confirmation
        return redirect()->back()->with('success', 'Mot-clé ajouté avec succès.');
    }
}
