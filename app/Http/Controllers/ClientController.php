<?php
namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Service;
use App\Models\DailySchedule;
use App\Models\Booking;
use App\Models\WorkBreak;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ClientController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:client')->except(['projects', 'showProject']);
    }

    public function dashboard(Request $request)
    {
        Log::info('ClientController::dashboard started', ['params' => $request->all()]);
        $client = Auth::guard('client')->user();
        $query = Booking::where('client_id', $client->id)
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
        Log::info('ClientController::dashboard completed', ['bookings_count' => $bookings->count()]);
        return view('client.dashboard', compact('client', 'bookings'));
    }

    public function projects(Request $request)
    {
        $projects = Auth::guard('client')->user()->projects()->with('specialists')->paginate(10);
        return view('client.projects', compact('projects'));
    }

    public function showProject($slug, Request $request)
    {
        Log::info('ClientController::showProject started', ['slug' => $slug, 'params' => $request->all()]);
        $project = Project::where('slug', $slug)
            ->with(['services' => function ($query) {
                $query->select('id', 'project_id', 'name', 'duration', 'price', 'category_id');
            }, 'categories'])
            ->firstOrFail();
        $categories = $project->categories;
        $services = $project->services;
        $date = $request->input('date', now()->format('Y-m-d'));

        $slotsByService = [];
        foreach ($services as $service) {
            $slotsByService[$service->id] = $this->getAvailableSlots($project, $service, $date);
        }

        Log::info('ClientController::showProject completed', ['slots_count' => count($slotsByService)]);
        return view('client.project', compact('project', 'categories', 'services', 'slotsByService', 'date'));
    }

    protected function getAvailableSlots(Project $project, Service $service, $date)
    {
        Log::info('getAvailableSlots started', [
            'project_id' => $project->id,
            'service_id' => $service->id,
            'date' => $date,
            'service_duration' => $service->duration
        ]);

        $minDuration = Service::where('project_id', $project->id)->min('duration') ?? 30;
        $slotStep = $minDuration;

        $dailySchedule = DailySchedule::where('project_id', $project->id)
            ->where('date', $date)
            ->where('is_working_day', true)
            ->select('id', 'project_id', 'start_time', 'end_time')
            ->first();

        if (!$dailySchedule) {
            Log::warning('No daily schedule found', ['project_id' => $project->id, 'date' => $date]);
            return [];
        }

        $bookings = Booking::where('project_id', $project->id)
            ->where('daily_schedule_id', $dailySchedule->id)
            ->select('id', 'start_time', 'service_id')
            ->with(['service' => function ($query) {
                $query->select('id', 'duration');
            }])
            ->get();

        $breaks = WorkBreak::where('daily_schedule_id', $dailySchedule->id)
            ->select('id', 'start_time', 'end_time')
            ->get();

        $slots = [];
        $currentTime = Carbon::parse($date . ' ' . $dailySchedule->start_time);
        $endTime = Carbon::parse($date . ' ' . $dailySchedule->end_time);
        $duration = $service->duration;
        $iterationCount = 0;

        Log::debug('getAvailableSlots params', [
            'currentTime' => $currentTime->toDateTimeString(),
            'endTime' => $endTime->toDateTimeString(),
            'slotStep' => $slotStep,
            'service_duration' => $duration,
            'min_duration' => $minDuration,
            'bookings_count' => $bookings->count(),
            'breaks_count' => $breaks->count()
        ]);

        while ($currentTime->lte($endTime->copy()->subMinutes($duration)) && $iterationCount < 1000) {
            $slotEnd = $currentTime->copy()->addMinutes($duration);
            $isFree = true;

            foreach ($bookings as $booking) {
                $bookingStart = Carbon::parse($booking->start_time);
                $bookingEnd = $bookingStart->copy()->addMinutes($booking->service->duration);
                if ($currentTime->lt($bookingEnd) && $slotEnd->gt($bookingStart)) {
                    $isFree = false;
                    Log::debug('Slot overlaps with booking', [
                        'slot_start' => $currentTime->toDateTimeString(),
                        'slot_end' => $slotEnd->toDateTimeString(),
                        'booking_start' => $bookingStart->toDateTimeString(),
                        'booking_end' => $bookingEnd->toDateTimeString()
                    ]);
                    break;
                }
            }

            foreach ($breaks as $break) {
                $breakStart = Carbon::parse($date . ' ' . $break->start_time);
                $breakEnd = Carbon::parse($date . ' ' . $break->end_time);
                if ($currentTime->lt($breakEnd) && $slotEnd->gt($breakStart)) {
                    $isFree = false;
                    Log::debug('Slot overlaps with break', [
                        'slot_start' => $currentTime->toDateTimeString(),
                        'slot_end' => $slotEnd->toDateTimeString(),
                        'break_start' => $breakStart->toDateTimeString(),
                        'break_end' => $breakEnd->toDateTimeString()
                    ]);
                    break;
                }
            }

            if ($isFree && $currentTime->gte(Carbon::parse($date . ' ' . $dailySchedule->start_time)) && $slotEnd->lte($endTime)) {
                $slots[] = [
                    'start' => $currentTime->format('H:i'),
                    'end' => $slotEnd->format('H:i'),
                ];
                Log::debug('Slot added', [
                    'start' => $currentTime->format('H:i'),
                    'end' => $slotEnd->format('H:i')
                ]);
            }

            $currentTime = $this->getNextSlotTime($currentTime, $slotStep, $bookings, $breaks, $dailySchedule, $service, $date);
            $iterationCount++;
            Log::debug('Next slot time', ['currentTime' => $currentTime->toDateTimeString()]);
        }

        if ($iterationCount >= 1000) {
            Log::error('Possible infinite loop in getAvailableSlots', ['project_id' => $project->id, 'service_id' => $service->id]);
        }

        Log::info('getAvailableSlots completed', ['slots_count' => count($slots)]);
        return $slots;
    }

    protected function getNextSlotTime(Carbon $currentTime, int $slotStep, $bookings, $breaks, DailySchedule $dailySchedule, Service $service, $date): Carbon
    {
        $scheduleStart = Carbon::parse($date . ' ' . $dailySchedule->start_time);
        $scheduleEnd = Carbon::parse($date . ' ' . $dailySchedule->end_time);
        $nextTime = $currentTime->copy();
        $roundingInterval = 15;

        Log::debug('getNextSlotTime started', [
            'currentTime' => $currentTime->toDateTimeString(),
            'slotStep' => $slotStep
        ]);

        $slotEnd = $nextTime->copy()->addMinutes($service->duration);
        $latestEnd = $nextTime;
        $lastBookingDuration = null;

        foreach ($bookings as $booking) {
            $bookingStart = Carbon::parse($booking->start_time);
            $bookingEnd = $bookingStart->copy()->addMinutes($booking->service->duration);
            if ($currentTime->lt($bookingEnd) && $slotEnd->gt($bookingStart)) {
                if ($bookingEnd->gt($latestEnd)) {
                    $latestEnd = $bookingEnd->copy();
                    $lastBookingDuration = $booking->service->duration;
                }
                Log::debug('Overlap with booking', [
                    'booking_start' => $bookingStart->toDateTimeString(),
                    'booking_end' => $bookingEnd->toDateTimeString(),
                    'current_slot_start' => $currentTime->toDateTimeString(),
                    'current_slot_end' => $slotEnd->toDateTimeString()
                ]);
            }
        }

        foreach ($breaks as $break) {
            $breakStart = Carbon::parse($date . ' ' . $break->start_time);
            $breakEnd = Carbon::parse($date . ' ' . $break->end_time);
            if ($currentTime->lt($breakEnd) && $slotEnd->gt($breakStart)) {
                if ($breakEnd->gt($latestEnd)) {
                    $latestEnd = $breakEnd->copy();
                    $lastBookingDuration = null;
                }
                Log::debug('Overlap with break', [
                    'break_start' => $breakStart->toDateTimeString(),
                    'break_end' => $breakEnd->toDateTimeString(),
                    'current_slot_start' => $currentTime->toDateTimeString(),
                    'current_slot_end' => $slotEnd->toDateTimeString()
                ]);
            }
        }

        if ($latestEnd->gt($nextTime)) {
            $nextTime = $latestEnd->copy();
            Log::debug('Adjusted to latest end', ['new_next_time' => $nextTime->toDateTimeString()]);
            if ($lastBookingDuration && ($lastBookingDuration % 15 !== 0)) {
                $minutes = $nextTime->minute;
                $roundedMinutes = ceil($minutes / $roundingInterval) * $roundingInterval;
                $nextTime->setMinutes($roundedMinutes);
                if ($roundedMinutes >= 60) {
                    $nextTime->addHour()->setMinutes(0);
                }
                Log::debug('Rounded to 15-minute interval', [
                    'original_minutes' => $minutes,
                    'rounded_minutes' => $roundedMinutes,
                    'nextTime_rounded' => $nextTime->toDateTimeString()
                ]);
            }
        } else {
            $nextTime->addMinutes($slotStep);
            Log::debug('Added slotStep', ['new_next_time' => $nextTime->toDateTimeString(), 'slotStep' => $slotStep]);
        }

        return $nextTime;
    }

    public function createBooking(Request $request, $slug)
    {
        Log::info('createBooking started', ['slug' => $slug, 'input' => $request->all()]);

        $request->validate([
            'date' => 'required|date',
            'slot_start' => 'required',
            'service_id' => 'required|exists:services,id',
        ]);

        $project = Project::where('slug', $slug)->firstOrFail();
        $service = Service::findOrFail($request->service_id);
        $dailySchedule = DailySchedule::where('project_id', $project->id)
            ->where('date', $request->date)
            ->firstOrFail();

        $startTime = Carbon::parse($request->date . ' ' . $request->slot_start);

        $booking = Booking::create([
            'project_id' => $project->id,
            'service_id' => $service->id,
            'client_id' => Auth::guard('client')->id(),
            'daily_schedule_id' => $dailySchedule->id,
            'start_time' => $startTime,
            'status' => 'pending',
        ]);

        Log::info('Booking created', ['booking_id' => $booking->id]);
        return redirect()->route('client.project', $slug)->with('message', 'Запись создана');
    }

    public function bookings(Request $request)
    {
        Log::info('ClientController::bookings started', ['params' => $request->all()]);
        $client = Auth::guard('client')->user();
        $query = Booking::where('client_id', $client->id)
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
        Log::info('ClientController::bookings completed', ['bookings_count' => $bookings->count()]);
        return view('client.bookings', compact('bookings'));
    }

    public function addProjectToFavorites(Request $request, $slug)
    {
        Log::info('addProjectToFavorites started', ['slug' => $slug]);
        $project = Project::where('slug', $slug)->firstOrFail();
        $client = Auth::guard('client')->user();
        if ($client->projects()->where('project_id', $project->id)->exists()) {
            $client->projects()->detach($project->id);
            $message = 'Проект удалён из избранного';
        } else {
            $client->projects()->syncWithoutDetaching([$project->id]);
            $message = 'Проект добавлен в избранное';
        }
        Log::info('Project favorite toggled', ['project_id' => $project->id, 'client_id' => $client->id]);
        return redirect()->route('client.project', $slug)->with('message', $message);
    }

    public function cancelBooking(Request $request, Booking $booking)
    {
        Log::info('cancelBooking started', ['booking_id' => $booking->id]);
        if ($booking->client_id !== Auth::guard('client')->id()) {
            return response()->json(['error' => 'Недостаточно прав для отмены'], 403);
        }
        $booking->update(['status' => 'cancelled']);
        Log::info('Booking cancelled', ['booking_id' => $booking->id]);
        return response()->json(['message' => 'Запись отменена']);
    }
}
