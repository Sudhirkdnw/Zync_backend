<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FollowingList extends Model
{
    use HasFactory;
    public $table = "following_lists";

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
    
    public function followerUser()
    {
        return $this->hasOne(User::class, 'id', 'my_user_id');
    }

    public function story()
    {
        return $this->hasMany(Story::class, 'user_id', 'user_id');
    }



}
