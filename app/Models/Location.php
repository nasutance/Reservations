<?php

namespace App\Models;

// Importation des classes nécessaires pour Eloquent ORM
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

// Le modèle Location représente un lieu (salle de spectacle, théâtre, etc.)
class Location extends Model
{
    use HasFactory; // Active la prise en charge des model factories pour les tests/seeders

    // Attributs que l’on autorise à être assignés en masse
    protected $fillable = [
        'slug',                  // Identifiant court du lieu (utilisé pour l'URL)
        'designation',           // Nom officiel du lieu
        'address',               // Adresse postale du lieu
        'locality_postal_code',  // Clé étrangère vers la table localities
        'website',               // Site web du lieu (optionnel)
        'phone',                 // Numéro de téléphone du lieu (optionnel)
    ];

    // Nom de la table associée dans la base de données
    protected $table = 'locations';

    // On ne veut pas que Laravel gère les timestamps (created_at, updated_at)
    public $timestamps = false;

    // Relation N-1 : chaque lieu appartient à une localité (via postal_code)
    public function locality(): BelongsTo
    {
        // Clé étrangère = 'locality_postal_code', clé locale = 'postal_code' dans la table localities
        return $this->belongsTo(Locality::class, 'locality_postal_code', 'postal_code');
    }

    // Relation 1-N : un lieu peut accueillir plusieurs spectacles
    public function shows(): HasMany
    {
        return $this->hasMany(Show::class);
    }

    // Relation 1-N : un lieu peut accueillir plusieurs représentations
    public function representations(): HasMany
    {
        return $this->hasMany(Representation::class);
    }
}
