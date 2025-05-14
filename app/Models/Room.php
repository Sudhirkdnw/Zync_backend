<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;
    public $table = "rooms";

    public function interests()
    {
        return $this->hasMany(Interest::class, 'id', 'interest_ids');
    }
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'admin_id');
    }
    public function roomUser()
    {
        return $this->hasMany(RoomUser::class, 'room_id', 'id');
    }
}