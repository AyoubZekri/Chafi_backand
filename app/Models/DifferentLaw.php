<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class DifferentLaw extends Pivot
{
    protected $table = 'different_law';

    protected $fillable = [
        "name_ar",
        "name_fr",
        'law_id',
        'different_id',
        'index_link',
    ];

    public function law()
    {
        return $this->belongsTo(Law::class);
    }
}
