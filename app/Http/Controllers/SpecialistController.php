<?php
namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Specialist;
use App\Models\Invitation;
use App\Models\Booking;
use App\Models\Service;
use App\Models\DailySchedule;
use App\Models\WorkBreak;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Mail\InvitationMail;
use Illuminate\Support\Facades\Mail;

class SpecialistController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:specialist')->except(['viewInvitation', 'acceptInvitation']);
    }

    public function dashboard(Request $request)
    {
        Log::info('SpecialistController::dashboard started', ['params' => $request->all()]);
        $specialist = Auth::guard('specialist')->user();
        $projects = $specialist->projects()->with('specialists')->paginate(10);
        Log::info('SpecialistController::dashboard completed', ['projects_count' => $projects->count()]);
        return view('specialist.dashboard', compact('specialist', 'projects'));
    }

    public function projects(Request $request)
    {
        $projects = Auth::guard('specialist')->user()->projects()->with('specialists')->paginate(10);
        return view('specialist.projects', compact('projects'));
    }

    public function showProject($slug, Request $request)
    {
        Log::info('SpecialistController::showProject started', ['slug' => $slug, 'params' => $request->all()]);
        $project = Project::where('slug', $slug)->with(['services', 'categories', 'specialists'])->firstOrFail();
        $this->authorizeSpecialist($project, 'view_schedule');
        $date = $request->input('date', now()->format('Y-m-d'));
        $dailySchedule = $project->dailySchedules()->where('date', $date)->first();
        $bookings = $dailySchedule ? $dailySchedule->bookings()->with('service')->get() : collect([]);
        Log::info('SpecialistController::showProject completed', ['bookings_count' => $bookings->count()]);
        return view('specialist.project', compact('project', 'dailySchedule', 'bookings', 'date'));
    }

    public function createProject(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        $project = Project::create([
            'name' => $request->name,
            'description' => $request->description,
            'slug' => Str::slug($request->name),
            'balance' => 0.00,
        ]);
        Auth::guard('specialist')->user()->projects()->attach($project->id, [
            'permissions' => ['manage_schedule', 'view_schedule', 'manage_balance', 'manage_specialists', 'confirm_bookings', 'manual_bookings', 'manage_services'],
            'is_owner' => true,
        ]);
        return redirect()->route('specialist.projects')->with('message', 'Проект создан');
    }

    public function editProject(Request $request, $slug)
    {
        $project = Project::where('slug', $slug)->firstOrFail();
        $this->authorizeSpecialist($project, 'manage_services');
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        $project->update([
            'name' => $request->name,
            'description' => $request->description,
            'slug' => Str::slug($request->name),
        ]);
        return redirect()->route('specialist.project', $project->slug)->with('message', 'Проект обновлён');
    }

    public function deleteProject($slug)
    {
        $project = Project::where('slug', $slug)->firstOrFail();
        $this->authorizeSpecialist($project, 'manage_specialists');
        $project->delete();
        return redirect()->route('specialist.projects')->with('message', 'Проект удалён');
    }

    public function inviteSpecialist(Request $request, $slug)
    {
        $project = Project::where('slug', $slug)->firstOrFail();
        $this->authorizeSpecialist($project, 'manage_specialists');
        $request->validate(['email' => 'required|email']);
        $specialist = Specialist::where('email', $request->email)->first();
        $permissions = $request->input('permissions', []);
        $invitation = Invitation::create([
            'project_id' => $project->id,
            'specialist_id' => $specialist ? $specialist->id : null,
            'email' => $request->email,
            'token' => Str::random(32),
            'permissions' => $permissions,
            'status' => 'pending',
        ]);
        Mail::to($request->email)->send(new InvitationMail($invitation));
        return redirect()->route('specialist.project', $slug)->with('message', 'Приглашение отправлено');
    }

    public function managePermissions(Request $request, $slug)
    {
        $project = Project::where('slug', $slug)->firstOrFail();
        $this->authorizeSpecialist($project, 'manage_specialists');
        $request->validate([
            'permissions' => 'array',
            'permissions.*' => 'array',
        ]);
        foreach ($request->permissions as $specialistId => $permissions) {
            $project->specialists()->updateExistingPivot($specialistId, ['permissions' => $permissions]);
        }
        return redirect()->route('specialist.project', $slug)->with('message', 'Права обновлены');
    }

    public function addBalance(Request $request, $slug)
    {
        $project = Project::where('slug', $slug)->firstOrFail();
        $this->authorizeSpecialist($project, 'manage_balance');
        $request->validate(['amount' => 'required|numeric|min:0']);
        $project->increment('balance', $request->amount);
        return redirect()->route('specialist.project', $slug)->with('message', 'Баланс пополнен');
    }

    public function confirmBooking(Request $request, $slug, Booking $booking)
    {
        $project = Project::where('slug', $slug)->firstOrFail();
        $this->authorizeSpecialist($project, 'confirm_bookings');
        $booking->update(['status' => 'confirmed']);
        return redirect()->route('specialist.project', $slug)->with('message', 'Запись подтверждена');
    }

    public function manualBooking(Request $request, $slug)
    {
        $project = Project::where('slug', $slug)->firstOrFail();
        $this->authorizeSpecialist($project, 'manual_bookings');
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'service_id' => 'required|exists:services,id',
            'date' => 'required|date',
            'start_time' => 'required',
        ]);
        $dailySchedule = $project->dailySchedules()->where('date', $request->date)->firstOrFail();
        Booking::create([
            'project_id' => $project->id,
            'service_id' => $request->service_id,
            'client_id' => $request->client_id,
            'daily_schedule_id' => $dailySchedule->id,
            'start_time' => $request->start_time,
            'status' => 'confirmed',
        ]);
        return redirect()->route('specialist.project', $slug)->with('message', 'Запись добавлена');
    }

    public function bookings(Request $request)
    {
        Log::info('SpecialistController::bookings started', ['params' => $request->all()]);
        $specialist = Auth::guard('specialist')->user();
        $query = Booking::whereIn('project_id', $specialist->projects()->pluck('projects.id'))
            ->with(['project', 'service', 'dailySchedule'])
            ->orderBy('start_time', 'desc');
        if ($request->has('status') && in_array($request->status, ['pending', 'confirmed', 'cancelled'])) {
            $query->where('status', $request->status);
        }
        if ($request->has('date')) {
            $query->whereHas('dailySchedule', function ($q) use ($request) {
                $q->where('date', $request->date);
            });
        }
        $bookings = $query->paginate(10);
        Log::info('SpecialistController::bookings completed', ['bookings_count' => $bookings->count()]);
        return view('specialist.bookings', compact('bookings'));
    }

    public function schedule(Request $request)
    {
        Log::info('SpecialistController::schedule started', ['params' => $request->all()]);
        $specialist = Auth::guard('specialist')->user();
        $projects = $specialist->projects()->pluck('projects.id');
        $schedules = DailySchedule::whereIn('project_id', $projects)
            ->where('date', '>=', now()->startOfDay())
            ->with('workBreaks', 'bookings')
            ->orderBy('date')
            ->get();
        Log::info('SpecialistController::schedule completed', ['schedules_count' => $schedules->count()]);
        return view('specialist.schedule', compact('schedules'));
    }

    public function viewInvitation($token)
    {
        $invitation = Invitation::where('token', $token)->firstOrFail();
        if (Auth::guard('specialist')->check() && $invitation->email === Auth::guard('specialist')->user()->email) {
            return view('specialist.invitation.view', compact('invitation'));
        }
        return redirect()->route('specialist.auth.login')->with('error', 'Авторизуйтесь для просмотра приглашения');
    }

    public function acceptInvitation($token)
    {
        $invitation = Invitation::where('token', $token)->firstOrFail();
        $specialist = Auth::guard('specialist')->user();
        if ($invitation->email !== $specialist->email) {
            abort(403);
        }
        $invitation->update([
            'specialist_id' => $specialist->id,
            'status' => 'accepted',
        ]);
        $invitation->project->specialists()->attach($specialist->id, [
            'permissions' => $invitation->permissions,
            'is_owner' => false,
        ]);
        return redirect()->route('specialist.projects')->with('message', 'Приглашение принято');
    }

    protected function authorizeSpecialist(Project $project, $permission)
    {
        $specialist = Auth::guard('specialist')->user();
        if (!$project->specialists()->where('specialist_id', $specialist->id)->exists()) {
            abort(403);
        }
        $pivot = $project->specialists()->where('specialist_id', $specialist->id)->first()->pivot;
        if (!$pivot->is_owner && !in_array($permission, $pivot->permissions ?? [])) {
            abort(403);
        }
    }
}
