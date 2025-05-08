<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;
use App\Models\Representation;

class RepresentationPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // public, affiliés, membres, admins
    }

    public function view(User $user, Representation $representation): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function update(User $user, Representation $representation): bool
    {
        return $user->hasRole('admin');
    }

    public function delete(User $user, Representation $representation): bool
    {
        return $user->hasRole('admin');
    }
}
