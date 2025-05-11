<?php

namespace App\Models;

// Importation des éléments nécessaires d'Eloquent
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

// Le modèle Price représente un tarif (normal, réduit, étudiant, etc.)
class Price extends Model
{
    use HasFactory; // Active la gestion des model factories

    // Attributs qui peuvent être remplis via un formulaire ou une requête
    protected $fillable = [
        'type',        // Libellé du tarif (ex : normal, étudiant, senior)
        'price',       // Montant en euros
        'description', // Explication du tarif (conditions, public concerné, etc.)
        'start_date',  // Date de début de validité du tarif
        'end_date',    // Date de fin de validité du tarif
    ];

    // Nom de la table correspondante
    protected $table = 'prices';

    // Pas de colonnes created_at / updated_at
    public $timestamps = false;

    // Relation Many-to-Many avec les spectacles :
    // un tarif peut être applicable à plusieurs spectacles
    public function shows(): BelongsToMany
    {
        return $this->belongsToMany(Show::class);
    }

    // Relation Many-to-Many personnalisée avec la table pivot 'representation_reservation'
    // Cela permet de lier un tarif à une réservation spécifique
    public function reservations()
    {
        return $this->belongsToMany(Reservation::class, 'representation_reservation')
                    ->withPivot('representation_id', 'quantity');
        // ⚠️ Cette relation permet de savoir :
        // - pour quelle représentation un tarif a été utilisé
        // - combien de billets ont été achetés avec ce tarif
    }
}
