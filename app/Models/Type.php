<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany; 

class Type extends Model
{
  use HasFactory;
  /**
  * The attributes that are mass assignable.
  *
  * @var array
  */
  protected $fillable = ['type'];
  /**
  * The table associated with the model.
  *
  * @var string
  */
  protected $table = 'types';
  /**
  * Indicates if the model should be timestamped.
  *
  * @var bool
  */
  public $timestamps = false;

  public function artists(): BelongsToMany
  {
    return $this->belongsToMany(Artist::class);
  }
}
