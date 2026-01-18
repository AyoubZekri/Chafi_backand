<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Mypath extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id','person_type','nataire_activity_id',
        'activity_id','tax_id','activit_special'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    public function natareActivity()
    {
        return $this->belongsTo(NataireActivity::class);
    }
}
