<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtpCode extends Model
{
    protected $fillable = ['identifier', 'type', 'code', 'expires_at', 'used'];
}