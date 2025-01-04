<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Locality extends Model
{
    use HasFactory;

    protected  $fillable = [
      'postal_code',
      'locality',
    ];

    protected $table = 'localities';

    protected $primaryKey = 'postal_code';

    public $timestamps = false;

    /**
    * Get the locations for the locality.
    */

    public function locations(): HasMany
    {
      return $this->hasMany(Location::class);
    }
}
