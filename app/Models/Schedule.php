<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $fillable = ['project_id', 'master_id', 'start_time', 'end_time', 'type', 'floating_break_buffer'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function master()
    {
        return $this->belongsTo(Master::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}