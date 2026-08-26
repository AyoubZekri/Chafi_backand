<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'image','type','title','title2','body','title_fr','title2_fr','body_fr',
        'read_time', 'chafi_advice', 'chafi_advice_fr', 'legal_source', 'legal_source_fr'
    ];
}
