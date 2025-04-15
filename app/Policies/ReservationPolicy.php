<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Reservation;

class ReservationPolicy
{
    /**
     * L’utilisateur peut voir n’importe quelle réservation s’il est admin.
     */
    public function viewAny(User $user)
    {
        return $user->hasRole('admin');
    }

    /**
     * Voir une réservation : admin OU propriétaire.
     */
    public function view(User $user, Reservation $reservation)
    {
        return $user->id === $reservation->user_id || $user->hasRole('admin');
    }

    /**
     * Modifier une réservation : uniquement le propriétaire.
     */
    public function update(User $user, Reservation $reservation)
    {
        return $user->id === $reservation->user_id;
    }

    /**
     * Supprimer une réservation : propriétaire OU admin.
     */
    public function delete(User $user, Reservation $reservation)
    {
        return $user->id === $reservation->user_id || $user->hasRole('admin');
    }

    /**
     * Créer une réservation : tout membre connecté.
     */
    public function create(User $user)
    {
        return $user->hasRole('member');
    }
}
