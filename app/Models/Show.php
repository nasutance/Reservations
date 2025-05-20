<?php

namespace App\Models;

// Importation des traits et classes nécessaires à Eloquent
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

// Le modèle Show représente un spectacle (pièce de théâtre, performance, etc.)
class Show extends Model
{
    use HasFactory, SoftDeletes; // SoftDeletes permet de "supprimer" sans effacer de la base

    /**
     * Attributs qui peuvent être remplis automatiquement
     */
    protected $fillable = [
        'slug',         // Identifiant unique (pour l’URL)
        'title',        // Titre du spectacle
        'description',  // Résumé ou synopsis
        'poster_url',   // URL de l'affiche (image)
        'duration',     // Durée du spectacle (en minutes)
        'created_in',   // Date ou année de création
        'location_id',  // Lieu principal de création (clé étrangère)
        'bookable',     // Booléen indiquant si le spectacle est réservable
    ];

    /**
     * Casting automatique des attributs : ici 'bookable' est converti en booléen
     */
    protected $casts = [
        'bookable' => 'boolean',
    ];

    /**
     * Nom explicite de la table liée au modèle
     */
    protected $table = 'shows';

    /**
     * Active la gestion des timestamps (created_at et updated_at)
     */
    public $timestamps = true;

    /**
     * Relation N-1 : un spectacle est rattaché à un lieu principal
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * Relation 1-N : un spectacle peut avoir plusieurs représentations (dates/heures)
     */
    public function representations(): HasMany
    {
        return $this->hasMany(Representation::class);
    }

    /**
     * Relation 1-N : un spectacle peut avoir plusieurs avis
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Relation N-N : un spectacle fait intervenir plusieurs artistes avec un rôle (ArtistType)
     */
    public function artistTypes(): BelongsToMany
    {
        return $this->belongsToMany(ArtistType::class);
    }

    /**
     * Relation N-N : un spectacle peut être associé à plusieurs tarifs (Price)
     */
    public function prices(): BelongsToMany
    {
        return $this->belongsToMany(Price::class);
    }

}
