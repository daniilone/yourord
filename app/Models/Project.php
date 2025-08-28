<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Project extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'slug', 'balance'];

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

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function specialists()
    {
        return $this->belongsToMany(Specialist::class, 'project_specialists')
            ->withPivot('permissions', 'is_owner');
    }

    public function owner()
    {
        return $this->specialists()->wherePivot('is_owner', true)->first();
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
