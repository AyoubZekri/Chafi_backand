<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class LawTaxAndApp extends Pivot
{
    protected $table = 'law_taxs_and_app';

    protected $fillable = [
        "name_ar",
        "name_fr",
        'law_id',
        'taxs_and_app_id',
        'index_link',
    ];

    public function law()
    {
        return $this->belongsTo(Law::class);
    }
}
