<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailySchedule extends Model
{
    protected $fillable = ['project_id', 'date', 'is_working_day', 'start_time', 'end_time'];
    protected $dates = ['date']; // Автоматическое преобразование date в Carbon

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function workBreaks()
    {
        return $this->hasMany(WorkBreak::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
