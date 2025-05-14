<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostContent extends Model
{
    use HasFactory;
    public $table = "post_contents";

    public function post()
    {
        return $this->hasOne(Post::class, 'id', 'post_id');
    }
}
