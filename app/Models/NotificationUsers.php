<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationUsers extends Model
{
    protected $fillable = [
        'user_id','is_read','notification_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function Notification()
    {
        return $this->belongsTo(Notification::class);
    }
}
