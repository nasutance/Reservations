<?php

namespace App\Models;

// Importation des traits et classes nécessaires à Eloquent
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

// Déclaration du modèle Locality, représentant une localité
class Locality extends Model
{
    use HasFactory; // Permet d'utiliser les factories pour les tests ou les seeders

    // Attributs que l’on autorise à remplir automatiquement via une requête (ex. : formulaire)
    protected $fillable = [
        'postal_code', // Code postal de la localité
        'locality',    // Nom de la localité
    ];

    // Spécifie explicitement le nom de la table si différent de la convention Laravel
    protected $table = 'localities';

    // Déclare que la clé primaire est 'postal_code' au lieu de l'id par défaut
    protected $primaryKey = 'postal_code';

    // Indique que la clé primaire n’est pas un entier auto-incrémenté
    public $incrementing = false;

    // Spécifie que la clé primaire est une chaîne de caractères
    protected $keyType = 'string';

    // Désactive les colonnes created_at et updated_at de Laravel
    public $timestamps = false;

    // Déclaration de la relation 1-n : une localité peut avoir plusieurs lieux (locations)
    public function locations(): HasMany
    {
        // On précise ici les clés étrangères et locales pour une meilleure clarté
        return $this->hasMany(Location::class, 'locality_postal_code', 'postal_code');
    }
}
