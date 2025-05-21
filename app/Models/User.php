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
use Filament\Models\Contracts\HasName;

// Le modèle User représente un utilisateur de l’application (membre, administrateur, affilié, etc.)
// Il hérite d'Authenticatable pour intégrer le système d’authentification de Laravel
// Il implémente MustVerifyEmail pour activer la vérification d'adresse e-mail
/**
 * 
 *
 * @property int $id
 * @property string|null $login
 * @property string|null $firstname
 * @property string $lastname
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $langue
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Reservation> $reservations
 * @property-read int|null $reservations_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Review> $reviews
 * @property-read int|null $reviews_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereFirstname($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLangue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLastname($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLogin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutTrashed()
 * @mixin \Eloquent
 */
class User extends Authenticatable implements MustVerifyEmail, HasName
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
        return $this->roles()->where('role', $role)->exists();
    }
    

    /**
     * Vérifie si l'utilisateur possède au moins un des rôles fournis
     */
    public function hasAnyRole(array $roles): bool
    {
        return $this->roles()->whereIn('role', $roles)->exists();
    }

    public function getFilamentName(): string
    {
        return trim("{$this->firstname} {$this->lastname}") ?: $this->email;
    }
    

}
