<?php

namespace App\Policies;

use App\Models\Show;
use App\Models\User;
use Illuminate\Auth\Access\Response;

/**
 * Politique d'autorisation pour les actions liées au modèle Show (spectacle).
 * Définit les permissions en fonction du rôle de l'utilisateur.
 */
class ShowPolicy
{
    /**
     * Autorise l'affichage de la liste des spectacles.
     *
     * @param User $user
     * @return bool
     *
     * Accessible aux membres, affiliés et administrateurs.
     * Les visiteurs anonymes n'ont pas accès via cette méthode (sauf override public).
     */
    public function viewAny(User $user)
    {
        return $user->hasAnyRole(['admin', 'affiliate', 'member']);
    }

    /**
     * Autorise l'affichage d'un spectacle en particulier.
     *
     * @param User $user
     * @param Show $show
     * @return bool
     *
     * Identique à viewAny : seuls les utilisateurs authentifiés ayant un rôle sont autorisés.
     */
    public function view(User $user, Show $show)
    {
        return $user->hasAnyRole(['admin', 'affiliate', 'member']);
    }

    /**
     * Autorise la création d'un nouveau spectacle.
     *
     * @param User $user
     * @return bool
     *
     * Seuls les administrateurs ont ce droit.
     */
    public function create(User $user)
    {
        return $user->hasRole('admin');
    }

    /**
     * Autorise la modification d'un spectacle.
     *
     * @param User $user
     * @param Show $show
     * @return bool
     *
     * Action réservée aux administrateurs.
     */
    public function update(User $user, Show $show)
    {
        return $user->hasRole('admin');
    }

    /**
     * Autorise la suppression d'un spectacle.
     *
     * @param User $user
     * @param Show $show
     * @return bool
     *
     * Action critique, donc limitée aux administrateurs.
     */
    public function delete(User $user, Show $show)
    {
        return $user->hasRole('admin');
    }

    /**
     * Autorise l’affectation des prix à un spectacle.
     *
     * @param User $user
     * @param Show $show
     * @return bool
     *
     * Géré uniquement par les administrateurs.
     */
    public function assignPrices(User $user, Show $show)
    {
        return $user->hasRole('admin');
    }

    /**
     * Autorise l’ajout de tags (mots-clés ou catégories) à un spectacle.
     *
     * @param User $user
     * @param Show $show
     * @return bool
     *
     * Seul l’administrateur peut modifier les métadonnées.
     */
    public function addTag(User $user, Show $show)
    {
        return $user->hasRole('admin');
    }

    /**
     * Autorise l’exportation des données des spectacles (CSV, PDF, etc.).
     *
     * @param User $user
     * @return bool
     *
     * L’export est considéré comme une opération sensible, donc réservée à l’admin.
     */
    public function export(User $user)
    {
        return $user->hasRole('admin');
    }
}
