<?php

namespace App\Policies;

use App\Models\Artist;
use App\Models\User;

class ArtistPolicy
{
    public function viewAny(User $user)
    {
        return true; // Admins + membres + visiteurs
    }

    public function view(User $user, Artist $artist)
    {
        return true; // Idem
    }

    public function create(User $user)
    {
        return $user->hasRole('admin');
    }

    public function update(User $user, Artist $artist)
    {
        return $user->hasRole('admin');
    }

    public function delete(User $user, Artist $artist)
    {
        return $user->hasRole('admin');
    }
}
