<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reservation extends Model
{
  use HasFactory;
  /**
  * The attributes that are mass assignable.
  *
  * @var array
  */

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
}
