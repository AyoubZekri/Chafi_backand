<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categories_institutions extends Model
{
    use HasFactory;

    protected $fillable = ['index', 'name', 'name_fr', 'type_cat'];

    public function CatInst()
    {
        return $this->hasMany(Categories_cat_insts::class, 'cat_id');
    }
}
