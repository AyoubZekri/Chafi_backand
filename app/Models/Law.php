<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Law extends Model
{
    use HasFactory;

    protected $fillable = ['name',"index",'name_fr','published_date','pdf'];

    public function institutions()
    {
        return $this->hasMany(Institution::class);
    }

    public function taxAndApps()
    {
        return $this->hasMany(TaxAndApp::class);
    }

    public function differents()
    {
        return $this->hasMany(Different::class);
    }
}

