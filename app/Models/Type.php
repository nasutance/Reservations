<?php

namespace App\Models;

// Importation des classes nécessaires
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

// Le modèle Type représente un métier ou une fonction artistique (ex : comédien, metteur en scène, scénographe, etc.)
/**
 * 
 *
 * @property int $id
 * @property string $type
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Artist> $artists
 * @property-read int|null $artists_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Type newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Type newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Type query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Type whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Type whereType($value)
 * @mixin \Eloquent
 */
class Type extends Model
{
    use HasFactory; // Active les model factories pour les tests/seeders

    /**
     * Attributs pouvant être remplis automatiquement
     */
    protected $fillable = ['type']; // Nom du type d’artiste (ex : "acteur", "scénographe", etc.)

    /**
     * Nom de la table associée dans la base de données
     */
    protected $table = 'types';

    /**
     * Indique que le modèle ne gère pas les timestamps (created_at / updated_at)
     */
    public $timestamps = false;

    /**
     * Relation Many-to-Many : un type peut concerner plusieurs artistes
     * Par défaut, Laravel attend une table pivot 'artist_type'
     */
    public function artists(): BelongsToMany
    {
        return $this->belongsToMany(Artist::class);
    }
}
