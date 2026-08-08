<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AppointmentDate extends Model
{
    use HasFactory;

    protected $fillable = [
        'appointment_id',
        'appointment_date',
        'alert_date',
    ];

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }
}
