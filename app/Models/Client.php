<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Client extends Authenticatable
{
    protected $fillable = ['email', 'name'];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function authProviders()
    {
        return $this->hasMany(AuthProvider::class, 'user_id')->where('user_type', 'client');
    }
}
