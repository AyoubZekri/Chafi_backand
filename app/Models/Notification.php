<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        "tax_id",'title','title_fr','timer','content','content_fr','type_notification'
    ];

    public function notifications()
    {
        return $this->hasMany(NotificationUsers::class);
    }
}
