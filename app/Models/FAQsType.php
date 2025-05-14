<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FAQsType extends Model
{
    use HasFactory;
    public $table = "faqs_types";

    public function faqs()
    {
        return $this->hasMany(FAQs::class, 'faqs_type_id', 'id');
    }
}
