<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Categories_differents extends Model
{
    use HasFactory;

    protected $fillable = ['index', 'name', 'name_fr', 'tax_id', 'type_cat'];

    public function different()
    {
        return $this->hasMany(Different::class, 'cat_id');
    }

}
