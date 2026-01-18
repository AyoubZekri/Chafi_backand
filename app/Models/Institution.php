<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Institution extends Model
{
    use HasFactory;

    protected $fillable = [
        'type_institution',"index",'scope','title','body','title_fr','body_fr',
        'law_id','index_link','calcul'
    ];

    public function law()
    {
        return $this->belongsTo(Law::class);
    }

    public function reads()
    {
        return $this->hasMany(ReadInstitution::class);
    }
}
