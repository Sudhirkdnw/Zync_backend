<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Interest extends Model
{
    use HasFactory;

    public $table = "interests";

    public function room()
    {
        return $this->hasOne(Room::class, 'interest_ids', 'id');
    }   


}
