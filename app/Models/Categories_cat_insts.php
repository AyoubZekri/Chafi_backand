<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Categories_cat_insts extends Model
{
    use HasFactory;
    protected $table = "categories_cat_insts";
    protected $fillable = ['index', 'name', 'name_fr', "cat_id"];

    public function Categories_institutions()
    {
        return $this->belongsTo(Categories_institutions::class, 'cat_id');
    }
}
