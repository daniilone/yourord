<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Project;
use App\Models\Service;
use App\Models\Schedule;
use App\Models\Blacklist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MasterController extends Controller
{
    public function dashboard()
    {
        return view('master.dashboard');
    }

    public function bookings()
    {
        $bookings = Booking::whereIn('project_id', Auth::guard('master')->user()->projects->pluck('id'))
            ->with(['service', 'schedule', 'project'])
            ->get();
        return view('master.bookings', compact('bookings'));
    }

    public function createManualBooking(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'service_id' => 'required|exists:services,id',
            'schedule_id' => 'required|exists:schedules,id',
            'client_email' => 'required|email',
            'client_name' => 'nullable|string',
        ]);

        $schedule = Schedule::findOrFail($request->schedule_id);
        $service = Service::findOrFail($request->service_id);

        // Проверка длительности
        $startTime = Carbon::parse($schedule->start_time);
        $endTime = $startTime->copy()->addMinutes($service->duration);
        if ($endTime > $schedule->end_time) {
            return redirect()->back()->withErrors(['time' => 'Услуга не помещается в слот']);
        }

        // Проверка конфликтов
        if (Booking::where('schedule_id', $request->schedule_id)->where('status', 'confirmed')->exists()) {
            return redirect()->back()->withErrors(['time' => 'Время занято']);
        }

        if (Blacklist::where('master_id', Auth::guard('master')->id())->where('client_email', $request->client_email)->exists()) {
            return redirect()->back()->withErrors(['client_email' => 'Клиент в черном списке']);
        }

        Booking::create([
            'project_id' => $request->project_id,
            'client_id' => null,
            'client_email' => $request->client_email,
            'client_name' => $request->client_name,
            'service_id' => $request->service_id,
            'schedule_id' => $request->schedule_id,
            'status' => 'confirmed',
        ]);

        return redirect()->route('master.bookings')->with('message', 'Запись создана');
    }

    public function schedules()
    {
        $schedules = Schedule::where('master_id', Auth::guard('master')->id())->with('project')->get();
        return view('master.schedules', compact('schedules'));
    }

    public function storeSchedule(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'type' => 'required|in:work,break,day_off',
            'floating_break_buffer' => 'nullable|integer|min:0',
        ]);

        Schedule::create([
            'project_id' => $request->project_id,
            'master_id' => Auth::guard('master')->id(),
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'type' => $request->type,
            'floating_break_buffer' => $request->floating_break_buffer,
        ]);

        return redirect()->route('master.schedules')->with('message', 'Слот добавлен');
    }
}
