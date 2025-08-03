<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = ['master_id', 'name', 'description', 'slug'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($project) {
            if (empty($project->slug)) {
                $project->slug = Str::slug($project->name);
                $baseSlug = $project->slug;
                $suffix = 1;
                while (self::where('slug', $project->slug)->exists()) {
                    $project->slug = $baseSlug . '-' . $suffix++;
                }
            }
        });
    }
    public function master()
    {
        return $this->belongsTo(Master::class);
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function dailySchedules()
    {
        return $this->hasMany(DailySchedule::class);
    }

    public function dailyScheduleTemplates()
    {
        return $this->hasMany(DailyScheduleTemplate::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
    public function clients()
    {
        return $this->belongsToMany(Client::class, 'client_projects', 'project_id', 'client_id');
    }

}
