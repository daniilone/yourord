<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Project;
use App\Models\Service;
use App\Models\Category;
use App\Models\DailySchedule;
use App\Models\WorkBreak;
use App\Models\DailyScheduleTemplate;
use App\Models\DailyScheduleTemplateBreak;
use App\Models\Blacklist;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class MasterController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:master');
    }

    public function dashboard()
    {
        $master = Auth::guard('master')->user();
        $projects = Project::where('master_id', $master->id)->get();
        $todayBookings = Booking::whereHas('project', function($query) use ($master) {
            $query->where('master_id', $master->id);
        })
            ->whereDate('start_time', now()->toDateString())
            ->count();
        $upcomingBookings = Booking::whereHas('project', function($query) use ($master) {
            $query->where('master_id', $master->id);
        })
            ->with(['project', 'service', 'client'])
            ->where('start_time', '>=', now())
            ->orderBy('start_time')
            ->take(5)
            ->get();
        $servicesCount = Service::whereHas('category', function($query) use ($master) {
            $query->whereHas('project', function($q) use ($master) {
                $q->where('master_id', $master->id);
            });
        })
            ->count();
        $monthlyEarnings = Booking::whereHas('project', function($query) use ($master) {
            $query->where('master_id', $master->id);
        })
            ->join('services', 'bookings.service_id', '=', 'services.id')
            ->where('bookings.status', 'completed')
            ->whereBetween('bookings.created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->sum('services.price');

        return view('master.dashboard', compact(
            'master',
            'projects',
            'todayBookings',
            'upcomingBookings',
            'servicesCount',
            'monthlyEarnings'
        ));
    }

    public function bookings()
    {
        $master = Auth::guard('master')->user();
        $bookings = Booking::whereHas('project', function ($query) use ($master) {
            $query->where('master_id', $master->id);
        })->with(['project', 'service', 'dailySchedule', 'client'])
            ->orderBy('start_time', 'desc')
            ->paginate(10); // Замените get() на paginate(10)
        return view('master.bookings', compact('bookings'));
    }

    public function updateBooking(Request $request, Booking $booking)
    {
        $master = Auth::guard('master')->user();
        if (!$master->projects->contains($booking->project_id)) {
            return redirect()->back()->withErrors(['booking' => 'У вас нет доступа к этой записи']);
        }

        $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled',
        ]);

        $booking->update(['status' => $request->status]);
        return redirect()->route('master.bookings')->with('message', 'Статус записи обновлен');
    }

    public function projects()
    {
        $master = Auth::guard('master')->user();
        $projects = Project::where('master_id', $master->id)->get();
        return view('master.projects', compact('projects'));
    }

    public function createProject(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $master = Auth::guard('master')->user();
        Project::create([
            'master_id' => $master->id,
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->route('master.projects')->with('message', 'Проект создан');
    }

    public function updateProject(Request $request, Project $project)
    {
        $master = Auth::guard('master')->user();
        if ($project->master_id !== $master->id) {
            return redirect()->back()->withErrors(['project' => 'У вас нет доступа к этому проекту']);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $project->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->route('master.projects')->with('message', 'Проект обновлен');
    }

    public function categories()
    {
        $master = Auth::guard('master')->user();
        $categories = Category::whereHas('project', function ($query) use ($master) {
            $query->where('master_id', $master->id);
        })->get();
        return view('master.categories', compact('categories'));
    }

    public function createCategory(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'name' => 'required|string|max:255',
        ]);

        $master = Auth::guard('master')->user();
        if (!Project::where('id', $request->project_id)->where('master_id', $master->id)->exists()) {
            return redirect()->back()->withErrors(['project' => 'У вас нет доступа к этому проекту']);
        }

        Category::create([
            'project_id' => $request->project_id,
            'name' => $request->name,
        ]);

        return redirect()->route('master.categories')->with('message', 'Категория создана');
    }

    public function updateCategory(Request $request, Category $category)
    {
        $master = Auth::guard('master')->user();
        if (!Project::where('id', $category->project_id)->where('master_id', $master->id)->exists()) {
            return redirect()->back()->withErrors(['project' => 'У вас нет доступа к этому проекту']);
        }

        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'name' => 'required|string|max:255',
        ]);

        if (!Project::where('id', $request->project_id)->where('master_id', $master->id)->exists()) {
            return redirect()->back()->withErrors(['project' => 'У вас нет доступа к этому проекту']);
        }

        $category->update([
            'project_id' => $request->project_id,
            'name' => $request->name,
        ]);

        return redirect()->route('master.categories')->with('message', 'Категория обновлена');
    }

    public function deleteCategory(Category $category)
    {
        $master = Auth::guard('master')->user();
        if (!Project::where('id', $category->project_id)->where('master_id', $master->id)->exists()) {
            return redirect()->back()->withErrors(['project' => 'У вас нет доступа к этому проекту']);
        }

        if ($category->services()->exists()) {
            return redirect()->back()->withErrors(['category' => 'Нельзя удалить категорию, так как она содержит услуги']);
        }

        $category->delete();
        return redirect()->route('master.categories')->with('message', 'Категория удалена');
    }

    public function services()
    {
        $master = Auth::guard('master')->user();
        $services = Service::whereHas('project', function ($query) use ($master) {
            $query->where('master_id', $master->id);
        })->with('category')->paginate(10);
        return view('master.services', compact('services'));
    }

    public function createService(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'duration' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
        ]);

        $master = Auth::guard('master')->user();
        if (!Project::where('id', $request->project_id)->where('master_id', $master->id)->exists()) {
            return redirect()->back()->withErrors(['project' => 'У вас нет доступа к этому проекту']);
        }

        if (!Category::where('id', $request->category_id)->where('project_id', $request->project_id)->exists()) {
            return redirect()->back()->withErrors(['category' => 'Категория не принадлежит этому проекту']);
        }

        Service::create([
            'project_id' => $request->project_id,
            'category_id' => $request->category_id,
            'name' => $request->name,
            'duration' => $request->duration,
            'price' => $request->price,
        ]);

        return redirect()->route('master.services')->with('message', 'Услуга создана');
    }

    public function updateService(Request $request, Service $service)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'duration' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
        ]);

        $master = Auth::guard('master')->user();
        if (!Project::where('id', $request->project_id)->where('master_id', $master->id)->exists()) {
            return redirect()->back()->withErrors(['project' => 'У вас нет доступа к этому проекту']);
        }

        if (!Category::where('id', $request->category_id)->where('project_id', $request->project_id)->exists()) {
            return redirect()->back()->withErrors(['category' => 'Категория не принадлежит этому проекту']);
        }

        $service->update([
            'project_id' => $request->project_id,
            'category_id' => $request->category_id,
            'name' => $request->name,
            'duration' => $request->duration,
            'price' => $request->price,
        ]);

        return redirect()->route('master.services')->with('message', 'Услуга обновлена');
    }

    public function deleteService(Service $service)
    {
        $master = Auth::guard('master')->user();
        if (!Project::where('id', $service->project_id)->where('master_id', $master->id)->exists()) {
            return redirect()->back()->withErrors(['project' => 'У вас нет доступа к этому проекту']);
        }

        if ($service->bookings()->exists()) {
            return redirect()->back()->withErrors(['service' => 'Нельзя удалить услугу, так как она используется в бронированиях']);
        }

        $service->delete();
        return redirect()->route('master.services')->with('message', 'Услуга удалена');
    }

    public function dailySchedules()
    {
        $master = Auth::guard('master')->user();
        $schedules = DailySchedule::whereHas('project', function ($query) use ($master) {
            $query->where('master_id', $master->id);
        })->with('workBreaks')->get();
        return view('master.daily_schedules', compact('schedules'));
    }

    public function createDailySchedule(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'date' => 'required|date',
            'is_working_day' => 'required|boolean',
            'start_time' => 'nullable|required_if:is_working_day,1|date_format:H:i,H:i:s',
            'end_time' => 'nullable|required_if:is_working_day,1|date_format:H:i,H:i:s|after:start_time',
            'breaks' => 'nullable|array',
            'breaks.*.start_time' => 'required_if:is_working_day,1|date_format:H:i,H:i:s',
            'breaks.*.end_time' => 'required_if:is_working_day,1|date_format:H:i,H:i:s|after:breaks.*.start_time',
        ]);

        $master = Auth::guard('master')->user();
        if (!Project::where('id', $request->project_id)->where('master_id', $master->id)->exists()) {
            return redirect()->back()->withErrors(['project' => 'У вас нет доступа к этому проекту']);
        }

        $schedule = DailySchedule::create([
            'project_id' => $request->project_id,
            'date' => $request->date,
            'is_working_day' => $request->is_working_day,
            'start_time' => $request->is_working_day ? $request->start_time : null,
            'end_time' => $request->is_working_day ? $request->end_time : null,
        ]);

        if ($request->is_working_day && $request->breaks) {
            foreach ($request->breaks as $break) {
                WorkBreak::create([
                    'daily_schedule_id' => $schedule->id,
                    'start_time' => $break['start_time'],
                    'end_time' => $break['end_time'],
                ]);
            }
        }

        return redirect()->route('master.daily_schedules')->with('message', 'Расписание дня создано');
    }

    public function updateDailySchedule(Request $request, DailySchedule $schedule)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'date' => 'required|date',
            'is_working_day' => 'required|boolean',
            'start_time' => 'nullable|required_if:is_working_day,1|date_format:H:i,H:i:s',
            'end_time' => 'nullable|required_if:is_working_day,1|date_format:H:i,H:i:s|after:start_time',
            'breaks' => 'nullable|array',
            'breaks.*.start_time' => 'required_if:is_working_day,1|date_format:H:i,H:i:s',
            'breaks.*.end_time' => 'required_if:is_working_day,1|date_format:H:i,H:i:s|after:breaks.*.start_time',
        ]);

        $master = Auth::guard('master')->user();
        if (!Project::where('id', $request->project_id)->where('master_id', $master->id)->exists()) {
            return redirect()->back()->withErrors(['project' => 'У вас нет доступа к этому проекту']);
        }

        $schedule->update([
            'project_id' => $request->project_id,
            'date' => $request->date,
            'is_working_day' => $request->is_working_day,
            'start_time' => $request->is_working_day ? $request->start_time : null,
            'end_time' => $request->is_working_day ? $request->end_time : null,
        ]);

        $schedule->workBreaks()->delete();

        if ($request->is_working_day && $request->breaks) {
            foreach ($request->breaks as $break) {
                WorkBreak::create([
                    'daily_schedule_id' => $schedule->id,
                    'start_time' => $break['start_time'],
                    'end_time' => $break['end_time'],
                ]);
            }
        }

        return redirect()->route('master.daily_schedules')->with('message', 'Расписание дня обновлено');
    }

    public function dailyScheduleTemplates()
    {
        $master = Auth::guard('master')->user();
        $templates = DailyScheduleTemplate::whereHas('project', function ($query) use ($master) {
            $query->where('master_id', $master->id);
        })->with('breaks')->get();
        return view('master.daily_schedule_templates', compact('templates'));
    }

    public function createDailyScheduleTemplate(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'name' => 'required|string|max:255',
            'is_working_day' => 'required|boolean',
            'start_time' => 'nullable|required_if:is_working_day,1|date_format:H:i,H:i:s',
            'end_time' => 'nullable|required_if:is_working_day,1|date_format:H:i,H:i:s|after:start_time',
            'breaks' => 'nullable|array',
            'breaks.*.start_time' => 'required_if:is_working_day,1|date_format:H:i,H:i:s',
            'breaks.*.end_time' => 'required_if:is_working_day,1|date_format:H:i,H:i:s|after:breaks.*.start_time',
        ]);

        $master = Auth::guard('master')->user();
        if (!Project::where('id', $request->project_id)->where('master_id', $master->id)->exists()) {
            return redirect()->back()->withErrors(['project' => 'У вас нет доступа к этому проекту']);
        }

        $template = DailyScheduleTemplate::create([
            'project_id' => $request->project_id,
            'name' => $request->name,
            'is_working_day' => $request->is_working_day,
            'start_time' => $request->is_working_day ? $request->start_time : null,
            'end_time' => $request->is_working_day ? $request->end_time : null,
        ]);

        if ($request->is_working_day && $request->breaks) {
            foreach ($request->breaks as $break) {
                DailyScheduleTemplateBreak::create([
                    'daily_schedule_template_id' => $template->id,
                    'start_time' => $break['start_time'],
                    'end_time' => $break['end_time'],
                ]);
            }
        }

        return redirect()->route('master.daily_schedule_templates')->with('message', 'Шаблон расписания создан');
    }

    public function applyDailyScheduleTemplate(Request $request)
    {
        $request->validate([
            'template_id' => 'required|exists:daily_schedule_templates,id',
            'date' => 'required|date',
        ]);

        $master = Auth::guard('master')->user();
        $template = DailyScheduleTemplate::with('breaks')->findOrFail($request->template_id);
        if (!Project::where('id', $template->project_id)->where('master_id', $master->id)->exists()) {
            return redirect()->back()->withErrors(['project' => 'У вас нет доступа к этому проекту']);
        }

        $schedule = DailySchedule::create([
            'project_id' => $template->project_id,
            'date' => $request->date,
            'is_working_day' => $template->is_working_day,
            'start_time' => $template->is_working_day ? $template->start_time : null,
            'end_time' => $template->is_working_day ? $template->end_time : null,
        ]);

        if ($template->is_working_day) {
            foreach ($template->breaks as $break) {
                WorkBreak::create([
                    'daily_schedule_id' => $schedule->id,
                    'start_time' => $break->start_time,
                    'end_time' => $break->end_time,
                ]);
            }
        }

        return redirect()->route('master.daily_schedules')->with('message', 'Шаблон расписания применен');
    }

    public function blacklist()
    {
        $master = Auth::guard('master')->user();
        $blacklist = Blacklist::where('master_id', $master->id)->with('client')->get();
        return view('master.blacklist', compact('blacklist'));
    }

    public function addToBlacklist(Request $request)
    {
        $request->validate([
            'client_email' => 'required|email',
            'reason' => 'nullable|string',
        ]);

        $master = Auth::guard('master')->user();
        $client = Client::where('email', $request->client_email)->first();

        Blacklist::create([
            'master_id' => $master->id,
            'client_id' => $client ? $client->id : null,
            'client_email' => $request->client_email,
            'reason' => $request->reason,
        ]);

        return redirect()->route('master.blacklist')->with('message', 'Клиент добавлен в черный список');
    }
}
