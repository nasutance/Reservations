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
/**
 * 
 *
 * @property int $id
 * @property string $slug
 * @property string $title
 * @property string|null $description
 * @property string|null $poster_url
 * @property int $duration
 * @property string $created_in
 * @property int|null $location_id
 * @property bool $bookable
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ArtistTypeShow> $artistTypeShow
 * @property-read int|null $artist_type_show_count
 * @property-read \App\Models\Location|null $location
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Price> $prices
 * @property-read int|null $prices_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Representation> $representations
 * @property-read int|null $representations_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Review> $reviews
 * @property-read int|null $reviews_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tag> $tags
 * @property-read int|null $tags_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Video> $videos
 * @property-read int|null $videos_count
 * @method static \Database\Factories\ShowFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Show newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Show newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Show onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Show query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Show whereBookable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Show whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Show whereCreatedIn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Show whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Show whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Show whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Show whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Show whereLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Show wherePosterUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Show whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Show whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Show whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Show withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Show withoutTrashed()
 * @mixin \Eloquent
 */
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
     * Relation N-N : un spectacle peut être associé à plusieurs tarifs (Price)
     */
    public function prices(): BelongsToMany
    {
        return $this->belongsToMany(Price::class);
    }

    public function tags() 
    {
        return $this->belongsToMany(Tag::class);
    }

    public function videos()
    {
        return $this->hasMany(Video::class);
    }      

    public function artistTypeShow()
    {
        return $this->hasMany(ArtistTypeShow::class);
    }

}
