<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterAuthProvider extends Model
{
    protected $table = 'master_auth_providers';
    protected $fillable = ['master_id', 'provider', 'provider_id'];

    public function master()
    {
        return $this->belongsTo(Master::class);
    }
}
