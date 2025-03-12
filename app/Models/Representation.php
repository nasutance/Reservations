<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Representation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'show_id',
        'schedule',
        'location_id',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'representations';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Get the actual location of the representation.
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * Get the show of the representation.
     */
    public function show(): BelongsTo
    {
        return $this->belongsTo(Show::class);
    }

    public function reservations()
    {
        return $this->belongsToMany(Reservation::class, 'representation_reservation')
            ->withPivot('price_id', 'quantity');
    }

}
