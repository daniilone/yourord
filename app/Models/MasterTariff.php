<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterTariff extends Model
{
    protected $fillable = ['master_id', 'tariff_id', 'start_date', 'end_date'];

    public function master()
    {
        return $this->belongsTo(Master::class);
    }

    public function tariff()
    {
        return $this->belongsTo(Tariff::class);
    }
}