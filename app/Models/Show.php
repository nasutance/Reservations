<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Show extends Model
{
    use HasFactory;
    /**
    * The attributes that are mass assignable.
    * @var array
    */
    protected $fillable =
    [
      'slug',
      'title',
      'description',
      'poster_url',
      'duration',
      'created_in',
      'location_postal_code',
      'bookable',
    ];

    /**
    * The table associated with the model.
    *
    * @var string
    */

    protected $table = 'shows';
    /**
    * Indicates if the model should be timestamped.
    *
    * @var bool
    */

    public $timestamps = true;
    /**
    * Get the main location of the show
    */
    public function location(): BelongsTo
    {
      return $this->belongsTo(Location::class);
    }

  }
