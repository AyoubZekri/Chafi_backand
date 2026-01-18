<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
   use HasApiTokens, HasFactory, Notifiable;
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username','email','role','wilaya','numperPhone',
        'image','gmail_id','token','password','notification_status'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = ['password','token'];

    public function notifications()
    {
        return $this->hasMany(NotificationUsers::class);
    }

    public function mypaths()
    {
        return $this->hasMany(Mypath::class);
    }

    public function readDifferent()
    {
        return $this->hasMany(ReadDifferent::class);
    }

    public function readTaxAndApp()
    {
        return $this->hasMany(ReadTaxAndApp::class);
    }

    public function readInstitution()
    {
        return $this->hasMany(ReadInstitution::class);
    }
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isUser()
    {
        return $this->role === 'user';
    }
}
