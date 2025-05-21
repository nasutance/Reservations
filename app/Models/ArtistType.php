<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Classe ArtistType – Représente l'association entre un artiste et un type (fonction).
 * 
 * Ex. : "Daniel Marcelin est un scénographe"
 * C'est un modèle pivot entre les entités Artist et Type.
 *
 * @property int $id
 * @property int $artist_id
 * @property int $type_id
 * @property-read \App\Models\Artist $artist
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ArtistTypeShow> $artistTypeShow
 * @property-read int|null $artist_type_show_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Show> $shows
 * @property-read int|null $shows_count
 * @property-read \App\Models\Type $type
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArtistType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArtistType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArtistType query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArtistType whereArtistId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArtistType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArtistType whereTypeId($value)
 * @mixin \Eloquent
 */
class ArtistType extends Model
{
    use HasFactory;

    /**
     * Champs autorisés à être remplis en masse (mass assignment).
     * Ici : les clés étrangères vers un artiste et un type.
     */
    protected $fillable = [
        'artist_id',
        'type_id',
    ];

    /**
     * Nom de la table associée au modèle.
     * On précise ici car ce n'est pas le pluriel standard.
     */
    protected $table = 'artist_type';

    /**
     * On désactive les timestamps automatiques (created_at et updated_at).
     */
    public $timestamps = false;

    /**
     * Relation Many-to-Many entre ArtistType et Show, via la table 'artist_type_show'.
     * Un artiste avec un type donné peut être lié à plusieurs spectacles (et vice versa).
     * 
     * Clés pivot :
     * - artist_type_id (clé étrangère dans la table pivot)
     * - show_id (clé étrangère dans la table pivot)
     */
    public function shows(): BelongsToMany
    {
        return $this->belongsToMany(Show::class, 'artist_type_show', 'artist_type_id', 'show_id');
    }

    /**
     * Relation inverse One-to-Many : chaque ArtistType appartient à un seul artiste.
     */
    public function artist(): BelongsTo
    {
        return $this->belongsTo(Artist::class);
    }

    /**
     * Relation inverse One-to-Many : chaque ArtistType appartient à un seul type.
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(Type::class);
    }

    public function artistTypeShow()
    {
        return $this->hasMany(ArtistTypeShow::class);
    }
}
