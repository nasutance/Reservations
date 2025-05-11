<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Reservation;

/**
 * Politique d'accès pour le modèle Reservation.
 * Cette classe gère les droits d'accès aux réservations selon le rôle de l'utilisateur.
 */
class ReservationPolicy
{
    /**
     * Autorise l'affichage de la liste de toutes les réservations.
     *
     * @param User $user
     * @return bool
     *
     * Seuls les administrateurs peuvent voir toutes les réservations.
     */
    public function viewAny(User $user)
    {
        return $user->hasRole('admin');
    }

    /**
     * Autorise l'affichage d'une réservation spécifique.
     *
     * @param User $user
     * @param Reservation $reservation
     * @return bool
     *
     * Un utilisateur peut consulter sa propre réservation,
     * ou toutes les réservations s’il est administrateur.
     */
    public function view(User $user, Reservation $reservation)
    {
        return $user->hasRole('admin') || $user->id === $reservation->user_id;
    }

    /**
     * Autorise la création d'une nouvelle réservation.
     *
     * @param User $user
     * @return bool
     *
     * Uniquement les membres ou affiliés peuvent créer une réservation.
     */
    public function create(User $user)
    {
        return $user->hasAnyRole(['member', 'affiliate']);
    }

    /**
     * Autorise la modification d'une réservation.
     *
     * @param User $user
     * @param Reservation $reservation
     * @return bool
     *
     * L'utilisateur peut modifier sa propre réservation,
     * ou toute réservation s’il est administrateur.
     */
    public function update(User $user, Reservation $reservation)
    {
        return $user->hasRole('admin') || $user->id === $reservation->user_id;
    }

    /**
     * Autorise la suppression d'une réservation.
     *
     * @param User $user
     * @param Reservation $reservation
     * @return bool
     *
     * L'utilisateur peut annuler (supprimer) sa propre réservation,
     * ou toute réservation s’il est administrateur.
     */
    public function delete(User $user, Reservation $reservation)
    {
        return $user->hasRole('admin') || $user->id === $reservation->user_id;
    }
}
