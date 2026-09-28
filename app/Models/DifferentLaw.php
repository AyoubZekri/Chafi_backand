<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DifferentLaw extends Model
{
    protected $table = 'different_law';

    protected $fillable = [
        "name_ar",
        "name_fr",
        'law_id',
        'different_id',
        'index_link',
        'article_id',
    ];

    public function law()
    {
        return $this->belongsTo(Law::class);
    }

    public function article()
    {
        return $this->belongsTo(Article::class, 'article_id', 'id');
    }
}
