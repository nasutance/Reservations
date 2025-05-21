<?php

namespace App\Models;
use App\Models\Artist;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Troupe extends Model
{
    protected $fillable = ['name', 'logo_url'];

    public function Artists()
    {
        return $this->belongsTo(Artists::class);
    }
}
