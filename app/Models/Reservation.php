<?php

namespace App\Models;

// Importation des traits et classes nécessaires à Eloquent
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

// Le modèle Reservation représente une réservation faite par un utilisateur pour une ou plusieurs représentations
class Reservation extends Model
{
    use HasFactory, SoftDeletes; // Permet d’utiliser les factories et la suppression "douce" (soft delete)

    /**
     * Déclare que le champ 'deleted_at' doit être traité comme une date
     * Ce champ est utilisé pour marquer une réservation comme "supprimée" sans l'effacer réellement de la base
     */
    protected $dates = ['deleted_at'];

    /**
     * Attributs remplissables en masse (par formulaire ou requête)
     */
    protected $fillable = [
        'user_id', // Référence vers l'utilisateur qui a effectué la réservation
        'status',  // État de la réservation (ex : en attente, payée, annulée)
    ];

    /**
     * Nom de la table dans la base de données
     */
    protected $table = 'reservations';

    /**
     * Active les timestamps Laravel, mais on personnalise le champ de création
     */
    public $timestamps = true;

    // Le champ created_at est renommé en booking_date (utile pour le suivi métier)
    const CREATED_AT = 'booking_date';

    /**
     * Relation N-1 : une réservation appartient à un seul utilisateur
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation N-N avec la table pivot 'representation_reservation'
     * Permet d’associer une réservation à plusieurs représentations,
     * avec des informations supplémentaires comme le tarif choisi et la quantité
     */
    public function representations()
    {
        return $this->belongsToMany(Representation::class, 'representation_reservation')
                    ->withPivot('price_id', 'quantity');
    }
}
