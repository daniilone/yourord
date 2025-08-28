<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class SmsCode extends Model
{
    protected $fillable = ['user_type', 'user_id', 'phone', 'code', 'expires_at'];
    protected $dates = ['expires_at'];
}
