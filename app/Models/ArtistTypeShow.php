<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
