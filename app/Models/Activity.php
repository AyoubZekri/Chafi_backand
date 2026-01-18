<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Activity extends Model
{
    use HasFactory;

    protected $table = 'activitys';

    protected $fillable = [
        "nataire_activitys_id","index",'name','body','name_fr','body_fr','tax_id','status_tax','code_activity'
    ];

    public function mypaths()
    {
        return $this->hasMany(Mypath::class);
    }

    public function NataireActivity()
    {
        return $this->belongsTo(NataireActivity::class,'nataire_activitys_id');
    }
}
