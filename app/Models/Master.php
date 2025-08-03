<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Master extends Authenticatable
{
    protected $fillable = ['email', 'name'];

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function masterTariffs()
    {
        return $this->hasMany(MasterTariff::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function blacklist()
    {
        return $this->hasMany(Blacklist::class);
    }
}