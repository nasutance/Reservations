<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Feed\Feedable;
use Spatie\Feed\FeedItem;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class Representation extends Model implements Feedable

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

    protected $casts = [
        'schedule' => 'datetime',
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

    public function prices()
{
    return $this->hasMany(Price::class);
}


public static function getFeedItems(): \Illuminate\Support\Collection
{
    return self::where('schedule', '>', now())
    ->orderBy('schedule')
    ->with('show', 'location')
    ->take(20)
    ->get(); // ✅ retourne une Collection

}


    public function toFeedItem(): FeedItem
    {
        return FeedItem::create([
            'id' => route('representations.show', $this),
            'title' => "{$this->show->title} à {$this->location->designation}",
            'summary' => "Le {$this->schedule->format('d/m/Y à H\hi')} à {$this->location->designation}",
            'updated' => new \Illuminate\Support\Carbon($this->schedule),
            'link' => route('representations.show', $this),
            'authorName' => '',
        ]);
    }



}
