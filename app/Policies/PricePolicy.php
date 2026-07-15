<?php

namespace App\Policies;

use App\Models\Price;
use App\Models\User;

/**
 * Politique d'accès pour le modèle Price.
 * La consultation des tarifs est publique ; leur gestion est réservée aux administrateurs.
 */
class PricePolicy
{
    /**
     * Détermine si l'utilisateur peut créer un nouveau tarif.
     * Réservé aux administrateurs.
     *
     * @return bool
     */
    public function create(User $user)
    {
        return $user->hasRole('admin');
    }

    /**
     * Détermine si l'utilisateur peut modifier un tarif existant.
     * Réservé aux administrateurs.
     *
     * @return bool
     */
    public function update(User $user, Price $price)
    {
        return $user->hasRole('admin');
    }

    /**
     * Détermine si l'utilisateur peut supprimer un tarif.
     * Réservé aux administrateurs.
     *
     * @return bool
     */
    public function delete(User $user, Price $price)
    {
        return $user->hasRole('admin');
    }
}
