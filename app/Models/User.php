<?php

namespace App\Models;

// Importations nécessaires pour l’authentification, les notifications et les relations
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

// Le modèle User représente un utilisateur de l’application (membre, administrateur, affilié, etc.)
// Il hérite d'Authenticatable pour intégrer le système d’authentification de Laravel
// Il implémente MustVerifyEmail pour activer la vérification d'adresse e-mail
class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes; 
    // HasApiTokens : pour l’auth via tokens (Sanctum)
    // SoftDeletes : permet de "supprimer" un utilisateur sans effacer ses données
    // Notifiable : permet d’envoyer des notifications (mail, etc.)

    /**
     * Attributs pouvant être remplis automatiquement (formulaire, seed, etc.)
     */
    protected $fillable = [
        'login',      // Nom d'utilisateur unique
        'firstname',  // Prénom
        'lastname',   // Nom
        'email',      // Adresse e-mail (doit être unique et vérifiée)
        'password',   // Mot de passe (hashé)
        'langue',     // Langue préférée de l'utilisateur
    ];

    /**
     * Attributs masqués lors de la sérialisation (ex. : API, JSON)
     */
    protected $hidden = [
        'password',         // Ne jamais exposer le mot de passe
        'remember_token',   // Jeton de session
    ];

    /**
     * Définition des conversions automatiques de types
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime', // Cast en objet Carbon
            'password' => 'hashed',            // Laravel hash automatiquement le mot de passe
        ];
    }

    /**
     * Relation 1-N : un utilisateur peut avoir plusieurs réservations
     */
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    /**
     * Relation 1-N : un utilisateur peut écrire plusieurs avis
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Relation N-N : un utilisateur peut avoir plusieurs rôles (membre, admin, critique, etc.)
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * Vérifie si l'utilisateur possède un rôle spécifique
     */
    public function hasRole(string $role): bool
    {
        return $this->roles->pluck('role')->contains($role);
    }

    /**
     * Vérifie si l'utilisateur possède au moins un des rôles fournis
     */
    public function hasAnyRole(array $roles): bool
    {
        return $this->roles->pluck('role')->intersect($roles)->isNotEmpty();
    }
}
