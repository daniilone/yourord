<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientAuthProvider extends Model
{
    protected $table = 'client_auth_providers';
    protected $fillable = ['client_id', 'provider', 'provider_id'];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
