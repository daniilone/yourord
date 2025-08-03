<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = ['master_id', 'tariff_id', 'amount', 'payment_system', 'status', 'transaction_id'];

    public function master()
    {
        return $this->belongsTo(Master::class);
    }

    public function tariff()
    {
        return $this->belongsTo(Tariff::class);
    }
}