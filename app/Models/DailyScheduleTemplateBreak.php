<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyScheduleTemplateBreak extends Model
{
    protected $fillable = ['daily_schedule_template_id', 'start_time', 'end_time'];

    public function dailyScheduleTemplate()
    {
        return $this->belongsTo(DailyScheduleTemplate::class);
    }
}
