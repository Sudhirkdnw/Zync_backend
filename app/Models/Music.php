<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Music extends Model
{
    use HasFactory;
    public $table = "musics";

    public function category()
    {
        return $this->hasOne(MusicCategory::class, 'id', 'category_id');
    }
    
    public function reel()
    {
        return $this->hasMany(Reel::class, 'music_id', 'id');
    }

}
