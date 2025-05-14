<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    public $table = "comments";

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
    
    public function likes()
    {
        return $this->hasMany(LikeComment::class);
    }
    
}
