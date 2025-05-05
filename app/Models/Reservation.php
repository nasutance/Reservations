<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reservation extends Model
{
  use HasFactory, SoftDeletes ;
  /**
  * The attributes that are mass assignable.
  *
  * @var array
  */

  // Déclare le champ 'deleted_at' pour qu'il soit traité comme une date
  protected $dates = ['deleted_at'];  

  protected $fillable =
  [
    'user_id',
    'status',
  ];
  /**
  * The table associated with the model.
  *
  * @var string
  */

  protected $table = 'reservations';
  /**
  * Indicates if the model should be timestamped.
  *
  * @var bool
  */

  public $timestamps = true;
  const CREATED_AT = 'booking_date';

  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class);
  }

  public function representations()
  {
      return $this->belongsToMany(Representation::class, 'representation_reservation')
          ->withPivot('price_id', 'quantity');
  }

}
