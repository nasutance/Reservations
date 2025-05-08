<?php

namespace App\Policies;

use App\Models\Show;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ShowPolicy
{
    public function viewAny(User $user)
    {
        return $user->hasAnyRole(['admin', 'affiliate', 'member']);
    }
    
    public function view(User $user, Show $show)
    {
        return $user->hasAnyRole(['admin', 'affiliate', 'member']);
    }
    
    public function create(User $user)
    {
        return $user->hasRole('admin');
    }

    public function update(User $user, Show $show)
    {
        return $user->hasRole('admin');
    }

    public function delete(User $user, Show $show)
    {
        return $user->hasRole('admin');
    }

    public function assignPrices(User $user, Show $show)
    {
        return $user->hasRole('admin');
    }

    public function addTag(User $user, Show $show)
    {
        return $user->hasRole('admin');
    }

    public function export(User $user)
    {
        return $user->hasRole('admin');
    }
}
