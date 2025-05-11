<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Representation;
use Illuminate\Auth\Access\Response;

/**
 * Politique d'autorisation pour le modèle Representation.
 * Elle détermine qui peut effectuer quelles actions sur les représentations d’un spectacle.
 */
class RepresentationPolicy
{
    /**
     * Autorise la consultation de toutes les représentations.
     *
     * @param User $user
     * @return bool
     *
     * Tous les utilisateurs, y compris les visiteurs non authentifiés, peuvent voir les représentations.
     */
    public function viewAny(User $user): bool
    {
        return true; // public, affiliés, membres, admins
    }

    /**
     * Autorise la consultation d'une représentation spécifique.
     *
     * @param User $user
     * @param Representation $representation
     * @return bool
     *
     * Tous les utilisateurs peuvent consulter une représentation particulière.
     */
    public function view(User $user, Representation $representation): bool
    {
        return true;
    }

    /**
     * Autorise la création d'une nouvelle représentation.
     *
     * @param User $user
     * @return bool
     *
     * Seuls les administrateurs peuvent créer une représentation.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Autorise la modification d'une représentation existante.
     *
     * @param User $user
     * @param Representation $representation
     * @return bool
     *
     * Seuls les administrateurs peuvent modifier une représentation.
     */
    public function update(User $user, Representation $representation): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Autorise la suppression d'une représentation.
     *
     * @param User $user
     * @param Representation $representation
     * @return bool
     *
     * Seuls les administrateurs peuvent supprimer une représentation.
     */
    public function delete(User $user, Representation $representation): bool
    {
        return $user->hasRole('admin');
    }
}
