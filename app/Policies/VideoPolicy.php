<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Video;

class VideoPolicy
{
    /**
     * Autoriser la création d'une vidéo.
     * Seuls les administrateurs peuvent ajouter des vidéos.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Autoriser la mise à jour d'une vidéo.
     * (utile si tu ajoutes l'édition dans l'admin)
     */
    public function update(User $user, Video $video): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Autoriser la suppression d'une vidéo.
     */
    public function delete(User $user, Video $video): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Affichage de la vidéo (toujours autorisé).
     */
    public function view(?User $user, Video $video): bool
    {
        return true; // même les visiteurs anonymes
    }

    /**
     * Affichage de toutes les vidéos (liste).
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }
}
