<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;



class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'login',
        'firstname',
        'lastname',
        'email',
        'password',
        'langue',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    /**
    * Get the reservations for the user.
    */
    public function reservations(): HasMany
    {
      return $this->hasMany(Reservation::class);
    }

    public function reviews(): HasMany
    {
      return $this->hasMany(Review::class);
    }

    public function roles(): BelongsToMany
    {
      return $this->belongsToMany(Role::class);
    }

    public function hasRole(string $role): bool
{
    return $this->roles->pluck('role')->contains($role);
}

public function hasAnyRole(array $roles): bool
{
    return $this->roles->pluck('role')->intersect($roles)->isNotEmpty();
}


}
