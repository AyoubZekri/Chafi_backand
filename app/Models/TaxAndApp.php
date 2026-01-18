<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TaxAndApp extends Model
{
    use HasFactory;

    protected $table = 'taxs_and_apps';

    protected $fillable = [
        "index",'cat_id','title','body','title_fr','body_fr','law_id','index_link','calcul'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class,'cat_id');
    }

    public function law()
    {
        return $this->belongsTo(Law::class);
    }

    public function reads()
    {
        return $this->hasMany(ReadTaxAndApp::class);
    }
}
