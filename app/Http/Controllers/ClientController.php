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

    public function projects(Request $request)
    {
        Log::info('ClientController::projects started', ['date' => $request->input('date')]);
        $date = $request->input('date', now()->format('Y-m-d'));
        $projects = Project::with(['services' => function ($query) {
            $query->select('id', 'project_id', 'name', 'duration', 'price');
        }])
            ->paginate(10);

        $slotsByService = [];
        foreach ($projects as $project) {
            foreach ($project->services as $service) {
                $slotsByService[$service->id] = $this->getAvailableSlots($project, $service, $date);
            }
        }

        Log::info('ClientController::projects completed', ['slots_count' => count($slotsByService)]);
        return view('client.projects', compact('projects', 'slotsByService', 'date'));
    }

    public function showProject($slug, Request $request)
    {
        Log::info('ClientController::showProject started', ['slug' => $slug, 'date' => $request->input('date')]);
        $project = Project::where('slug', $slug)
            ->with(['services' => function ($query) {
                $query->select('id', 'project_id', 'name', 'duration', 'price');
            }])
            ->firstOrFail();
        $date = $request->input('date', now()->format('Y-m-d'));

        $slotsByService = [];
        foreach ($project->services as $service) {
            $slotsByService[$service->id] = $this->getAvailableSlots($project, $service, $date);
        }

        Log::info('ClientController::showProject completed', ['slots_count' => count($slotsByService)]);
        return view('client.project', compact('project', 'slotsByService', 'date'));
    }

    protected function getAvailableSlots(Project $project, Service $service, $date)
    {
        Log::info('getAvailableSlots started', [
            'project_id' => $project->id,
            'service_id' => $service->id,
            'date' => $date,
            'service_duration' => $service->duration
        ]);

        $minDuration = Service::where('project_id', $project->id)
            ->select('duration')
            ->orderBy('duration')
            ->value('duration') ?? 30;

        $slotStep = 15; // Шаг 15 минут для точного выравнивания

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
            'service_duration' => $duration
        ]);

        while ($currentTime->lte($endTime->subMinutes($duration)) && $iterationCount < 1000) {
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
        $nextTime = $currentTime->copy()->addMinutes($slotStep);

        Log::debug('getNextSlotTime started', [
            'currentTime' => $currentTime->toDateTimeString(),
            'slotStep' => $slotStep,
            'nextTime_initial' => $nextTime->toDateTimeString()
        ]);

        // Проверка пересечений с бронированиями
        foreach ($bookings as $booking) {
            $bookingStart = Carbon::parse($booking->start_time);
            $bookingEnd = $bookingStart->copy()->addMinutes($booking->service->duration);
            $slotEnd = $currentTime->copy()->addMinutes($service->duration);
            if ($currentTime->lt($bookingEnd) && $slotEnd->gt($bookingStart)) {
                $nextTime = $bookingEnd->copy();
                Log::debug('Adjusted for booking overlap', [
                    'booking_start' => $bookingStart->toDateTimeString(),
                    'booking_end' => $bookingEnd->toDateTimeString(),
                    'new_next_time' => $nextTime->toDateTimeString()
                ]);
                break;
            }
        }

        // Проверка пересечений с перерывами
        foreach ($breaks as $break) {
            $breakStart = Carbon::parse($date . ' ' . $break->start_time);
            $breakEnd = Carbon::parse($date . ' ' . $break->end_time);
            $slotEnd = $currentTime->copy()->addMinutes($service->duration);
            if ($currentTime->lt($breakEnd) && $slotEnd->gt($breakStart)) {
                $nextTime = $breakEnd->copy();
                Log::debug('Adjusted for break overlap', [
                    'break_start' => $breakStart->toDateTimeString(),
                    'break_end' => $breakEnd->toDateTimeString(),
                    'new_next_time' => $nextTime->toDateTimeString()
                ]);
                break;
            }
        }

        // Выравнивание по шагу 15 минут относительно начала расписания
        $minutesSinceStart = $nextTime->diffInMinutes($scheduleStart);
        $alignedMinutes = ceil($minutesSinceStart / $slotStep) * $slotStep;
        $nextTime = $scheduleStart->copy()->addMinutes($alignedMinutes);

        Log::debug('Aligned next time', [
            'minutesSinceStart' => $minutesSinceStart,
            'alignedMinutes' => $alignedMinutes,
            'nextTime_final' => $nextTime->toDateTimeString()
        ]);

        // Проверка выхода за пределы рабочего дня
        if ($nextTime->gt($scheduleEnd->subMinutes($service->duration))) {
            $nextTime = $scheduleEnd->copy();
            Log::debug('Next time adjusted to schedule end', ['next_time' => $nextTime->toDateTimeString()]);
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

    public function bookings()
    {
        Log::info('ClientController::bookings started');
        $client = Auth::guard('client')->user();
        $bookings = Booking::where('client_id', $client->id)
            ->with(['project', 'service'])
            ->orderBy('start_time', 'desc')
            ->paginate(10);
        Log::info('ClientController::bookings completed', ['bookings_count' => $bookings->count()]);
        return view('client.bookings', compact('bookings'));
    }

    public function addProjectToFavorites(Request $request, $slug)
    {
        Log::info('addProjectToFavorites started', ['slug' => $slug]);
        $project = Project::where('slug', $slug)->firstOrFail();
        $client = Auth::guard('client')->user();
        $client->projects()->syncWithoutDetaching([$project->id]);
        Log::info('Project added to favorites', ['project_id' => $project->id, 'client_id' => $client->id]);
        return redirect()->route('client.project', $slug)->with('message', 'Проект добавлен в избранное');
    }

    public function cancelBooking(Request $request, Booking $booking)
    {
        Log::info('cancelBooking started', ['booking_id' => $booking->id]);
        if ($booking->client_id !== Auth::guard('client')->id()) {
            return redirect()->route('client.bookings')->with('error', 'Недостаточно прав для отмены');
        }
        $booking->update(['status' => 'canceled']);
        Log::info('Booking canceled', ['booking_id' => $booking->id]);
        return redirect()->route('client.bookings')->with('message', 'Запись отменена');
    }
}
