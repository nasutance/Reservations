<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Video extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'video_url', 'show_id'];

    public function shows() {return $this->belongsTo(Show::class);}
}
