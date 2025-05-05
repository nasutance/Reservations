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
      'start_date',
      'end_date',
    ];

    protected $table = 'prices';

    public $timestamps = false;


    public function shows(): BelongsToMany
    {
      return $this->belongsToMany(Show::class);
    }

    public function reservations()
{
    return $this->belongsToMany(Reservation::class, 'representation_reservation')
                ->withPivot('representation_id', 'quantity');
                //pour afficher toutes les réservations liées à un prix donnée
                //en sachant pour quelle représentation et combien de billets ont été achetés

}

}
