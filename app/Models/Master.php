<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Master extends Authenticatable
{
    protected $fillable = ['email', 'name'];

    public function authProviders()
    {
        return $this->hasMany(MasterAuthProvider::class);
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function tariffs()
    {
        return $this->hasMany(MasterTariff::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function blacklists()
    {
        return $this->belongsToMany(Client::class, 'blacklist', 'master_id', 'client_id')->withPivot('reason');
    }
}
