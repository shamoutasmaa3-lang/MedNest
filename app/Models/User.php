<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $guarded = ['id'];

   protected $fillable = [
    'name',
    'email',
    'password',
    'role',
    'address',   
];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    public function cart()
    {
    return $this->hasMany(Cart::class);
    }

    public function prescriptions()
    {
        return $this->hasMany(Prescription::class);
    }
    public function recommendations()
    {
    return $this->hasMany(Recommendation::class);
    }
}