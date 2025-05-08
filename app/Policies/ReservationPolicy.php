<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Reservation;

class ReservationPolicy
{
    public function viewAny(User $user)
    {
        return $user->hasRole('admin');
    }
  

    public function view(User $user, Reservation $reservation)
    {
        return $user->hasRole('admin') || $user->id === $reservation->user_id;
    }

    public function create(User $user)
    {
        return $user->hasAnyRole(['member', 'affiliate']);
    }

    public function update(User $user, Reservation $reservation)
    {
        return $user->hasRole('admin') || $user->id === $reservation->user_id;
    }

    public function delete(User $user, Reservation $reservation)
    {
        return $user->hasRole('admin') || $user->id === $reservation->user_id;
    }
}