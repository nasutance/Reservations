<?php

namespace App\Models;

// Importation des fonctionnalités d’Eloquent
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

// Le modèle Role représente un rôle attribué à un ou plusieurs utilisateurs (ex : admin, membre, critique, etc.)
class Role extends Model
{
    use HasFactory; // Permet de générer des instances de test avec des factories

    /**
     * Attributs pouvant être assignés en masse (ex: depuis un formulaire)
     */
    protected $fillable = [
        'role', // Nom du rôle (ex: member, admin, critic)
    ];

    /**
     * Spécifie la table liée dans la base de données
     */
    protected $table = 'roles';

    /**
     * Ce modèle n'utilise pas les colonnes created_at / updated_at
     */
    public $timestamps = false;

    /**
     * Relation Many-to-Many :
     * Un rôle peut être attribué à plusieurs utilisateurs
     * Cette relation s’appuie sur la table pivot "role_user" (ou "user_role" si personnalisée)
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }
}
