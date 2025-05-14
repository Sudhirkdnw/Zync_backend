<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MusicCategory extends Model
{
    use HasFactory;
    public $table = "music_categories";

    public function musics()
    {
        return $this->hasMany(Music::class, 'category_id', 'id')->where('is_deleted', Constants::DeletedNo);
    }   
}
