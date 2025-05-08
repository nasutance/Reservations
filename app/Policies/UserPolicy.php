<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Voir un utilisateur : soi-même ou admin
     */
    public function view(User $user, User $target)
    {
        return $user->id === $target->id || $user->hasRole('admin');
    }

    /**
     * Mettre à jour un utilisateur : soi-même ou admin
     */
    public function update(User $user, User $target)
    {
        return $user->id === $target->id || $user->hasRole('admin');
    }

    /**
     * Supprimer un utilisateur : soi-même ou admin
     */
    public function delete(User $user, User $target)
    {
        return $user->id === $target->id || $user->hasRole('admin');
    }
}