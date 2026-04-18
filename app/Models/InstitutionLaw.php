<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class InstitutionLaw extends Pivot
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
        return $this->belongsTo(Law::class);
    }
}
