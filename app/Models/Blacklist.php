<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Blacklist extends Model
{
    protected $fillable = ['master_id', 'client_id', 'client_email', 'reason'];

    public function master()
    {
        return $this->belongsTo(Master::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
