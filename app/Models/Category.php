<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['index','name','name_fr','tax_id','type_cat'];

    public function taxAndApps()
    {
        return $this->hasMany(TaxAndApp::class,'cat_id');
    }
}
