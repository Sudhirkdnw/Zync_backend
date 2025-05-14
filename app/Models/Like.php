<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    use HasFactory;
    public $table = "likes";

    protected $fillable = [
        'user_id',
        'comment_id',
        'reel_id',
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }   
}
