<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FAQs extends Model
{
    use HasFactory;
    public $table = "faqs";

    public function type()
    {
        return $this->hasOne(FAQsType::class, 'id', 'faqs_type_id');
    }
}
