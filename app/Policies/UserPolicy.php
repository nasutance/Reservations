<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

/**
 * Politique d'autorisation pour les actions liées au modèle User.
 * Gère les droits de modification et de consultation des comptes utilisateurs.
 */
class UserPolicy
{
    /**
     * Détermine si un utilisateur peut consulter un profil utilisateur.
     *
     * @param User $user    L'utilisateur connecté (demandeur)
     * @param User $target  L'utilisateur ciblé (profil à consulter)
     * @return bool
     *
     * Autorisé si l'utilisateur consulte son propre profil ou s'il est administrateur.
     */
    public function view(User $user, User $target)
    {
        return $user->id === $target->id || $user->hasRole('admin');
    }

    /**
     * Détermine si un utilisateur peut mettre à jour un profil.
     *
     * @param User $user    L'utilisateur connecté
     * @param User $target  L'utilisateur ciblé
     * @return bool
     *
     * Autorisé pour la modification de son propre compte, ou pour l'admin.
     */
    public function update(User $user, User $target)
    {
        return $user->id === $target->id || $user->hasRole('admin');
    }

    /**
     * Détermine si un utilisateur peut supprimer un compte utilisateur.
     *
     * @param User $user    L'utilisateur connecté
     * @param User $target  L'utilisateur ciblé
     * @return bool
     *
     * Un utilisateur peut supprimer son propre compte,
     * ou un administrateur peut supprimer n'importe quel compte.
     */
    public function delete(User $user, User $target)
    {
        return $user->id === $target->id || $user->hasRole('admin');
    }
}
