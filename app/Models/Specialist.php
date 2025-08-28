<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Specialist extends Authenticatable
{
    use Notifiable;

    protected $guard = 'specialist';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_specialists')
            ->withPivot('is_owner', 'permissions')
            ->withTimestamps();
    }
}
