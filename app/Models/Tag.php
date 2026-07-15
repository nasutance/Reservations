<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

// Le modèle Tag représente une étiquette de classement des spectacles (humour, drame, etc.)
class Tag extends Model
{
    use HasFactory;

    /**
     * Attributs remplissables en masse
     */
    protected $fillable = [
        'tag',
    ];

    /**
     * Relation N-N : un tag peut être associé à plusieurs spectacles
     */
    public function shows(): BelongsToMany
    {
        return $this->belongsToMany(Show::class, 'show_tag');
    }
}
