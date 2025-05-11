<?php

namespace App\Models;

// Importation du trait HasFactory pour la génération de données de test
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Le modèle Tag représente un mot-clé ou une étiquette associée à un ou plusieurs spectacles
class Tag extends Model
{
    use HasFactory;

    /**
     * Attributs pouvant être remplis via un formulaire ou une requête
     */
    protected $fillable = ['tag']; // Le nom du tag (ex : "comédie", "drame", "musique")

    /**
     * Relation N-N : un tag peut être associé à plusieurs spectacles
     * et un spectacle peut avoir plusieurs tags (table pivot attendue : show_tag)
     */
    public function shows()
    {
        return $this->belongsToMany(Show::class);
    }
}
