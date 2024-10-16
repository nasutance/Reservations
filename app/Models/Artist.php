<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class Artist extends Model

{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *champs qui ne devraient pas être modifiables avec la propriété $guarded ex:protected $guarded = ['id', 'password', 'role'];
     * @var array
     */
    protected $fillable = ['firstname', 'lastname'];
   /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'artists';
   /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false; /*Par défaut, Laravel crée des champs created_at et updated_at dans la table. Pour empêcher cela, vous pouvez assigner la valeur false à la propriété $timestamps.*/
}
