<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Service;
use App\Models\Category;
use App\Models\DailySchedule;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ClientController extends Controller
{
    public function showProject($slug)
    {
        $project = Project::where('slug', $slug)->firstOrFail();
        $categories = Category::where('project_id', $project->id)->get();
        $services = Service::where('project_id', $project->id)->get();

        // Получаем расписание на ближайшие дни
        $schedules = DailySchedule::where('project_id', $project->id)
            ->where('date', '>=', Carbon::today()->format('Y-m-d'))
            ->where('is_working_day', true)
            ->with('workBreaks', 'bookings')
            ->get();

        $timeSlots = [];
        foreach ($schedules as $schedule) {
            $date = Carbon::parse($schedule->date); // Парсим дату
            $start = Carbon::parse($schedule->date . ' ' . $schedule->start_time);
            $end = Carbon::parse($schedule->date . ' ' . $schedule->end_time);
            $breaks = $schedule->workBreaks->map(function ($break) use ($schedule) {
                return [
                    'start' => Carbon::parse($schedule->date . ' ' . $break->start_time),
                    'end' => Carbon::parse($schedule->date . ' ' . $break->end_time),
                ];
            });
            $bookings = $schedule->bookings->map(function ($booking) use ($schedule) {
                $start = Carbon::parse($schedule->date . ' ' . $booking->start_time);
                return [
                    'start' => $start,
                    'end' => $start->copy()->addMinutes($booking->service->duration),
                ];
            });

            $slots = [];
            $current = $start->copy();
            while ($current < $end) {
                $slotEnd = $current->copy()->addMinutes(30); // Слоты по 30 минут
                $isAvailable = true;

                foreach ($breaks as $break) {
                    if ($current->between($break['start'], $break['end']) || $slotEnd->between($break['start'], $break['end'])) {
                        $isAvailable = false;
                        break;
                    }
                }

                foreach ($bookings as $booking) {
                    if ($current->between($booking['start'], $booking['end']) || $slotEnd->between($booking['start'], $booking['end'])) {
                        $isAvailable = false;
                        break;
                    }
                }

                if ($isAvailable) {
                    $slots[] = $current->format('H:i');
                }
                $current->addMinutes(30);
            }

            $timeSlots[$date->format('Y-m-d')] = $slots;
        }

        return view('client.project', compact('project', 'categories', 'services', 'timeSlots'));
    }

    public function dashboard()
    {
        $client = Auth::guard('client')->user();
        return view('client.dashboard', compact('client'));
    }

    public function bookings()
    {
        $client = Auth::guard('client')->user();
        $bookings = Booking::where('client_id', $client->id)->with(['project', 'service'])->get();
        return view('client.bookings', compact('bookings'));
    }

    public function projects()
    {
        $projects = Project::all();
        return view('client.projects', compact('projects'));
    }

    public function addProjectToFavorites(Request $request, $slug)
    {
        $client = Auth::guard('client')->user();
        $project = Project::where('slug', $slug)->firstOrFail();
        $client->favoriteProjects()->attach($project->id);
        return redirect()->back()->with('message', 'Проект добавлен в избранное');
    }

    public function createBooking(Request $request, $slug)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
        ]);

        $client = Auth::guard('client')->user();
        $project = Project::where('slug', $slug)->firstOrFail();
        $service = Service::findOrFail($request->service_id);
        $schedule = DailySchedule::where('project_id', $project->id)
            ->where('date', $request->date)
            ->where('is_working_day', true)
            ->firstOrFail();

        $start = Carbon::parse($request->date . ' ' . $request->start_time);
        $end = $start->copy()->addMinutes($service->duration);

        // Проверка перерывов
        foreach ($schedule->workBreaks as $break) {
            $breakStart = Carbon::parse($request->date . ' ' . $break->start_time);
            $breakEnd = Carbon::parse($request->date . ' ' . $break->end_time);
            if ($start->between($breakStart, $breakEnd) || $end->between($breakStart, $breakEnd)) {
                return redirect()->back()->withErrors(['start_time' => 'Выбранное время попадает на перерыв']);
            }
        }

        // Проверка существующих записей
        $existingBooking = Booking::where('daily_schedule_id', $schedule->id)
            ->where('start_time', $request->start_time)
            ->where('status', '!=', 'cancelled')
            ->exists();
        if ($existingBooking) {
            return redirect()->back()->withErrors(['start_time' => 'Это время уже занято']);
        }

        Booking::create([
            'client_id' => $client->id,
            'project_id' => $project->id,
            'service_id' => $service->id,
            'daily_schedule_id' => $schedule->id,
            'client_email' => $client->email,
            'start_time' => $request->start_time,
            'status' => 'pending',
        ]);

        return redirect()->route('client.bookings')->with('message', 'Запись создана');
    }

    public function cancelBooking(Request $request, Booking $booking)
    {
        $client = Auth::guard('client')->user();
        if ($booking->client_id !== $client->id) {
            return redirect()->back()->withErrors(['booking' => 'У вас нет доступа к этой записи']);
        }

        $booking->update(['status' => 'cancelled']);
        return redirect()->route('client.bookings')->with('message', 'Запись отменена');
    }
}
