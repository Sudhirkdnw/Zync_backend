<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomUser extends Model
{
    use HasFactory;

    public $table = "room_users";

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function room()
    {
        return $this->hasOne(Room::class, 'id', 'room_id');
    }

    public function invited_user()
    {
        return $this->hasOne(User::class, 'id', 'invited_by');
    }


}