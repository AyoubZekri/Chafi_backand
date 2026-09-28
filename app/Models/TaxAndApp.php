<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TaxAndApp extends Model
{
    use HasFactory;

    protected $table = 'taxs_and_apps';

    protected $fillable = [
        "index",'cat_id','title','body','title_fr','body_fr','law_id','index_link','calcul', 'document_id', 'article_id'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class,'cat_id');
    }

    public function law()
    {
        return $this->belongsTo(Law::class);
    }

    public function laws()
    {
        return $this->hasMany(LawTaxAndApp::class, 'taxs_and_app_id');
    }

    public function reads()
    {
        return $this->hasMany(ReadTaxAndApp::class);
    }

    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function article()
    {
        return $this->belongsTo(Article::class);
    }
}
