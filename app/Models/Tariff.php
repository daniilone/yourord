<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tariff extends Model
{
    protected $fillable = ['name', 'price', 'duration_days'];

    public function masterTariffs()
    {
        return $this->hasMany(MasterTariff::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}