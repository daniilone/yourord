<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    protected $fillable = ['project_id', 'specialist_id', 'email', 'token', 'permissions', 'status'];
    protected $casts = ['permissions' => 'array'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function specialist()
    {
        return $this->belongsTo(Specialist::class);
    }
}
