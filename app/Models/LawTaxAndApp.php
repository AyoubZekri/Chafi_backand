<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LawTaxAndApp extends Model
{
    protected $table = 'law_taxs_and_app';

    protected $fillable = [
        "name_ar",
        "name_fr",
        'law_id',
        'taxs_and_app_id',
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
