<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReelComment extends Model
{
    use HasFactory;
    public $table = "reel_comments";

    protected $fillable = [
        'user_id',
        'reel_id',
        'description',
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}