<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyScheduleTemplate extends Model
{
    protected $fillable = ['project_id', 'name', 'is_working_day', 'start_time', 'end_time'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function breaks()
    {
        return $this->hasMany(DailyScheduleTemplateBreak::class);
    }
}
