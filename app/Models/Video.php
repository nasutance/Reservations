<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Show;

class Video extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'video_url', 'show_id'];

    public function show()
    {
        return $this->belongsTo(Show::class);
    }
}
