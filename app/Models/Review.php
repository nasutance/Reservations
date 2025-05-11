<?php

namespace App\Models;

// Importation des classes nécessaires
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// Le modèle Review représente un avis rédigé par un utilisateur à propos d’un spectacle
class Review extends Model
{
    use HasFactory; // Active les factories pour la génération de données fictives (tests, seeders)

    /**
     * Attributs qui peuvent être remplis automatiquement via des formulaires ou des requêtes
     */
    protected $fillable = [
        'user_id',     // Référence vers l’auteur de l’avis (utilisateur)
        'show_id',     // Référence vers le spectacle concerné
        'review',      // Contenu textuel de l’avis
        'stars',       // Note attribuée au spectacle (ex : 1 à 5 étoiles)
        'validated',   // Booléen indiquant si l’avis a été validé (modération)
        'created_at',  // Date de création (managée manuellement ici)
        'updated_at'   // Date de dernière modification (managée manuellement ici)
    ];

    /**
     * Nom de la table associée dans la base de données
     */
    protected $table = 'reviews';

    /**
     * Laravel ne gère pas automatiquement les timestamps ici
     * (les champs created_at et updated_at doivent être remplis manuellement si utilisés)
     */
    public $timestamps = false;

    /**
     * Relation N-1 : un avis est écrit par un utilisateur
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation N-1 : un avis concerne un seul spectacle
     */
    public function show(): BelongsTo
    {
        return $this->belongsTo(Show::class);
    }
}
