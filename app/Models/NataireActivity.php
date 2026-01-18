<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NataireActivity extends Model
{
    use HasFactory;

    protected $table = 'nataire_activitys';

    protected $fillable = ["index",'name',"name_fr"];

    public function mypaths()
    {
        return $this->hasMany(Mypath::class);
    }

    public function Activity()
    {
        return $this->hasMany(Activity::class);
    }
}
