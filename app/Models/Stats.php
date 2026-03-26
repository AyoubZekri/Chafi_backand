<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Stats extends Model
{
    use HasFactory;

    protected $fillable = [
        // "numper_enter",
        // "numper_enter_Guest",
        "device_id",
        "type_user",
        "state",
        "open_date"
    ];
}
