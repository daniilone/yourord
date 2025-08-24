<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = ['client_id', 'project_id', 'service_id', 'daily_schedule_id', 'client_email', 'start_time', 'status'];

    protected $dates = ['start_time', 'created_at', 'updated_at'];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function dailySchedule()
    {
        return $this->belongsTo(DailySchedule::class);
    }
}
