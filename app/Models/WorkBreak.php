<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkBreak extends Model
{
    protected $fillable = ['daily_schedule_id', 'start_time', 'end_time'];

    public function dailySchedule()
    {
        return $this->belongsTo(DailySchedule::class);
    }
}
