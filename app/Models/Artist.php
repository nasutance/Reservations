<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Troupe;

/**
 * Classe Artist – représente un artiste (acteur, metteur en scène, etc.)
 * Cette classe est liée à la table 'artists' dans la base de données.
 *
 * @property int $id
 * @property string $firstname
 * @property string $lastname
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ArtistType> $artistTypes
 * @property-read int|null $artist_types_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Type> $types
 * @property-read int|null $types_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Artist newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Artist newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Artist query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Artist whereFirstname($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Artist whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Artist whereLastname($value)
 * @mixin \Eloquent
 */
class Artist extends Model
{
    use HasFactory; // Fournit des méthodes pour générer des instances de modèle via des factories (tests, seeders, etc.)

    /**
     * Attributs modifiables en masse via des formulaires ou requêtes.
     * Attention : pour des raisons de sécurité, seuls les champs listés ici peuvent être mass-assignés.
     */
    protected $fillable = ['firstname', 'lastname'];

    /**
     * Table associée au modèle (utile si le nom n’est pas au pluriel par défaut).
     */
    protected $table = 'artists';

    /**
     * Désactive la gestion automatique des colonnes created_at et updated_at.
     * Cela suppose que ces colonnes ne sont pas présentes dans la table.
     */
    public $timestamps = false;

    /**
     * Relation Many-to-Many avec le modèle Type (ex : acteur, scénographe, etc.)
     * Laravel s’attend à une table pivot 'artist_type' (ou personnalisée).
     * Exemple : $artist->types renvoie tous les types liés à cet artiste.
     */
    public function types(): BelongsToMany
    {
        return $this->belongsToMany(Type::class);
    }

    /**
     * Relation One-to-Many : un artiste peut avoir plusieurs liens (ArtistType) vers des types spécifiques.
     * Cette relation est utile pour accéder aux objets de liaison intermédiaires.
     * Exemple : $artist->artistTypes renvoie tous les objets ArtistType liés.
     */
    public function artistTypes(): HasMany
    {
        return $this->hasMany(ArtistType::class);
    }

    public function Troupes(): BelongsToMany
    {
        return $this->belongsToMany(Troupe::class);
    }
}
