<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Bonusesandcompensations extends Model
{
    use HasFactory;
    protected $table = 'bonuses_and_compensations';


    protected $fillable = ['name_ar','name_fr','category','is_required',"type","has_special_logic"];

}
