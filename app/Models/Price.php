<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Price extends Model
{
    use HasFactory;

    protected  $fillable = [
      'type',
      'price',
      'description',
      'startDate',
      'endDate',
    ];

    protected $table = 'prices';

    public $timestamps = false;

    /**
    * Get the shows for which this price applies
    */
    public function shows(): BelongsToMany
    {
      return $this->belongsToMany(Show::class);
    } 
}
