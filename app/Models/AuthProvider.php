<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuthProvider extends Model
{
    protected $fillable = ['user_id', 'user_type', 'provider', 'provider_id'];
}