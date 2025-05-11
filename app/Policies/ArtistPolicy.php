<?php

namespace App\Policies;

use App\Models\Artist;
use App\Models\User;

/**
 * Politique d'accès pour le modèle Artist.
 * Elle détermine les autorisations que chaque utilisateur a sur les ressources Artist.
 */
class ArtistPolicy
{
    /**
     * Détermine si l'utilisateur peut voir la liste des artistes.
     * Tous les utilisateurs sont autorisés (accès public).
     *
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user)
    {
        return true; // Public, membres, affiliés, admins → tout le monde
    }

    /**
     * Détermine si l'utilisateur peut consulter un artiste spécifique.
     * Tous les utilisateurs sont autorisés.
     *
     * @param User $user
     * @param Artist $artist
     * @return bool
     */
    public function view(User $user, Artist $artist)
    {
        return true; // Idem
    }

    /**
     * Détermine si l'utilisateur peut créer un nouvel artiste.
     * Seuls les administrateurs sont autorisés à effectuer cette opération.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user)
    {
        return $user->hasRole('admin'); // Uniquement admin
    }

    /**
     * Détermine si l'utilisateur peut modifier un artiste existant.
     * Réservé aux administrateurs.
     *
     * @param User $user
     * @param Artist $artist
     * @return bool
     */
    public function update(User $user, Artist $artist)
    {
        return $user->hasRole('admin'); // Uniquement admin
    }

    /**
     * Détermine si l'utilisateur peut supprimer un artiste.
     * Réservé aux administrateurs.
     *
     * @param User $user
     * @param Artist $artist
     * @return bool
     */
    public function delete(User $user, Artist $artist)
    {
        return $user->hasRole('admin'); // Uniquement admin
    }
}
