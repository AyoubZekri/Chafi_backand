<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReadDifferent extends Model
{
    use HasFactory;

    protected $fillable = ['different_id','user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function different()
    {
        return $this->belongsTo(Different::class);
    }
}
