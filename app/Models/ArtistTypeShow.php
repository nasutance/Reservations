<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id
 * @property int $artist_type_id
 * @property int $show_id
 * @property-read \App\Models\ArtistType $artistType
 * @property-read \App\Models\Show $show
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArtistTypeShow newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArtistTypeShow newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArtistTypeShow query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArtistTypeShow whereArtistTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArtistTypeShow whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArtistTypeShow whereShowId($value)
 * @mixin \Eloquent
 */
class ArtistTypeShow extends Model
{
    public $timestamps = false; 

    protected $fillable = ['artist_type_id', 'show_id'];
    protected $table = 'artist_type_show';


    public function artistType()
    {
        return $this->belongsTo(ArtistType::class);
    }

    public function show()
    {
        return $this->belongsTo(Show::class);
    }
}
