<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Client extends Authenticatable
{
    protected $fillable = ['email', 'name'];

    public function authProviders()
    {
        return $this->hasMany(ClientAuthProvider::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function blacklists()
    {
        return $this->belongsToMany(Master::class, 'blacklist', 'client_id', 'master_id')->withPivot('reason');
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'client_projects', 'client_id', 'project_id');
    }
}
