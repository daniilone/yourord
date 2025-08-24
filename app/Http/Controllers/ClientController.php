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

class ClientController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:client')->except(['showProject', 'projects']);
    }

    public function dashboard()
    {
        $client = Auth::guard('client')->user();
        $bookings = Booking::where('client_id', $client->id)
            ->with(['project', 'service'])
            ->orderBy('start_time', 'desc')
            ->paginate(10);
        return view('client.bookings', compact('bookings'));
    }

    public function projects(Request $request)
    {
        $projects = Project::with('services')->get();
        $date = $request->input('date', now()->format('Y-m-d'));

        // Получаем слоты для каждой услуги в каждом проекте
        $slotsByService = [];
        foreach ($projects as $project) {
            foreach ($project->services as $service) {
                $slotsByService[$service->id] = $this->getAvailableSlots($project, $service, $date);
            }
        }

        return view('client.projects', compact('projects', 'slotsByService', 'date'));
    }

    public function showProject($projectSlug, Request $request)
    {
        $project = Project::where('slug', $projectSlug)->firstOrFail();
        $services = Service::where('project_id', $project->id)->get();
        $date = $request->input('date', now()->format('Y-m-d'));

        // Получаем слоты для каждой услуги
        $slotsByService = [];
        foreach ($services as $service) {
            $slotsByService[$service->id] = $this->getAvailableSlots($project, $service, $date);
        }

        return view('client.project', compact('project', 'services', 'slotsByService', 'date'));
    }

    protected function getAvailableSlots(Project $project, Service $service, $date)
    {
        // Находим минимальную длительность услуги для проекта
        $minDuration = Service::where('project_id', $project->id)->min('duration') ?? 30;

        // Округляем шаг до ближайшего значения, кратного 15 минутам
        $slotStep = ceil($minDuration / 15) * 15;

        // Получаем расписание проекта
        $dailySchedule = DailySchedule::where('project_id', $project->id)
            ->where('date', $date)
            ->where('is_working_day', true)
            ->first();

        if (!$dailySchedule) {
            return [];
        }

        // Получаем бронирования и перерывы
        $bookings = Booking::where('project_id', $project->id)
            ->where('daily_schedule_id', $dailySchedule->id)
            ->get();

        $breaks = WorkBreak::where('daily_schedule_id', $dailySchedule->id)->get();

        // Генерируем слоты
        $slots = [];
        $currentTime = Carbon::parse($dailySchedule->start_time);
        $endTime = Carbon::parse($dailySchedule->end_time);
        $duration = $service->duration;

        while ($currentTime->lte($endTime->subMinutes($duration))) {
            $slotEnd = $currentTime->copy()->addMinutes($duration);

            // Проверка, свободен ли слот
            $isFree = true;
            foreach ($bookings as $booking) {
                $bookingStart = Carbon::parse($booking->start_time);
                $bookingEnd = Carbon::parse($booking->end_time);
                if ($currentTime->lt($bookingEnd) && $slotEnd->gt($bookingStart)) {
                    $isFree = false;
                    break;
                }
            }
            foreach ($breaks as $break) {
                $breakStart = Carbon::parse($break->start_time);
                $breakEnd = Carbon::parse($break->end_time);
                if ($currentTime->lt($breakEnd) && $slotEnd->gt($breakStart)) {
                    $isFree = false;
                    break;
                }
            }

            if ($isFree) {
                $slots[] = [
                    'start' => $currentTime->format('H:i'),
                    'end' => $slotEnd->format('H:i'),
                ];
            }

            // Следующий слот с учётом шага
            $currentTime = $this->getNextSlotTime($currentTime, $slotStep, $bookings, $breaks, $dailySchedule);
        }

        return $slots;
    }

    protected function getNextSlotTime(Carbon $currentTime, int $slotStep, $bookings, $breaks, DailySchedule $dailySchedule): Carbon
    {
        $nextTime = $currentTime->copy()->addMinutes($slotStep);

        // Проверяем, не попадает ли следующий слот на бронирование или перерыв
        foreach ($bookings as $booking) {
            $bookingStart = Carbon::parse($booking->start_time);
            $bookingEnd = Carbon::parse($booking->end_time);
            if ($nextTime->between($bookingStart, $bookingEnd)) {
                $minutesToNextSlot = ceil($bookingEnd->diffInMinutes($currentTime->startOfDay()) / 15) * 15;
                return $currentTime->startOfDay()->addMinutes($minutesToNextSlot);
            }
        }

        foreach ($breaks as $break) {
            $breakStart = Carbon::parse($break->start_time);
            $breakEnd = Carbon::parse($break->end_time);
            if ($nextTime->between($breakStart, $breakEnd)) {
                $minutesToNextSlot = ceil($breakEnd->diffInMinutes($currentTime->startOfDay()) / 15) * 15;
                return $currentTime->startOfDay()->addMinutes($minutesToNextSlot);
            }
        }

        // Округляем до ближайшего значения, кратного 15 минутам
        $minutesToNextSlot = ceil($nextTime->diffInMinutes($currentTime->startOfDay()) / 15) * 15;
        return $currentTime->startOfDay()->addMinutes($minutesToNextSlot);
    }

    public function createBooking(Request $request, $slug)
    {
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
        $endTime = $startTime->copy()->addMinutes($service->duration);

        Booking::create([
            'project_id' => $project->id,
            'service_id' => $service->id,
            'client_id' => Auth::guard('client')->id(),
            'daily_schedule_id' => $dailySchedule->id,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'status' => 'pending',
        ]);

        return redirect()->route('client.project', $slug)->with('message', 'Запись создана');
    }
}
