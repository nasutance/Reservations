<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Locality extends Model
{
    use HasFactory;

    protected $fillable = [
        'postal_code',
        'locality',
    ];

    protected $table = 'localities';

    protected $primaryKey = 'postal_code';
    public $incrementing = false; // indispensable car ta clé est un string
    protected $keyType = 'string'; // indispensable

    public $timestamps = false;

    // Relation corrigée
    public function locations(): HasMany
    {
        return $this->hasMany(Location::class, 'locality_postal_code', 'postal_code');
    }
}
