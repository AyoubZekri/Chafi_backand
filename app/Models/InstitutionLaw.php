<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InstitutionLaw extends Model
{
    protected $table = 'institution_law';

    protected $fillable = [
        "name_ar",
        "name_fr",
        'law_id',
        'institution_id',
        'index_link',
    ];


    public function law()
    {
        return $this->belongsTo(Law::class, 'law_id', 'id');
    }
}
