<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Institution extends Model
{
    use HasFactory;

    protected $fillable = [
        'type_institution',
        "cat_id",
        "index",
        'scope',
        'title',
        'body',
        'title_fr',
        'body_fr',
        'law_id',
        'index_link',
        'calcul'
    ];

    public function categories_cat_insts()
    {
        return $this->belongsTo(Categories_cat_insts::class, 'cat_id');
    }


    public function law()
    {
        return $this->belongsTo(Law::class);
    }

    public function laws()
    {
        return $this->hasMany(InstitutionLaw::class, 'institution_id');
    }

    public function reads()
    {
        return $this->hasMany(ReadInstitution::class, 'institution_id', 'id');
    }

}
