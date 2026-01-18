<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        "index",'tax_id','declaration',"declaration_fr",'deadline','dependencies',"dependencies_fr"
    ];
}
