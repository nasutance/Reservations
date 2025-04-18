<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
      'location_id',
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

    /**
    * Get the representations of this show.
    */

    public function representations(): HasMany
    {
      return $this->hasMany(Representation::class);
    }

    public function reviews(): HasMany
    {
      return $this->hasMany(Review::class);
    }

    /**
    * Get the performances (artists in a type of collaboration) for the show
    */
    public function artistTypes(): BelongsToMany
    {
      return $this->belongsToMany(ArtistType::class);
    }

    public function prices(): BelongsToMany
    {
      return $this->belongsToMany(Price::class);
    }

    public function tags()
    {
      return $this->belongsToMany(Tag::class);
    }
    
  }
