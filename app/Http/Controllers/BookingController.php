<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Project;
use App\Models\Service;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    public function clientDashboard()
    {
        return view('client.dashboard');
    }

    public function clientBookings()
    {
        $bookings = Booking::where('client_id', Auth::guard('client')->id())->with(['service', 'schedule'])->get();
        return view('client.bookings', compact('bookings'));
    }

    public function create(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'service_id' => 'required|exists:services,id',
            'schedule_id' => 'required|exists:schedules,id',
        ]);

        $service = Service::findOrFail($request->service_id);
        $schedule = Schedule::findOrFail($request->schedule_id);

        // Проверка длительности
        $startTime = Carbon::parse($schedule->start_time);
        $endTime = $startTime->copy()->addMinutes($service->duration);
        if ($endTime > $schedule->end_time) {
            return redirect()->back()->withErrors(['time' => 'Услуга не помещается в слот']);
        }

        // Проверка перерывов
        $nextBreak = Schedule::where('project_id', $request->project_id)
            ->where('type', 'break')
            ->where('start_time', '>=', $startTime)
            ->orderBy('start_time')
            ->first();
        if ($nextBreak && $nextBreak->floating_break_buffer) {
            $bufferTime = $startTime->copy()->addMinutes($nextBreak->floating_break_buffer);
            if ($endTime > $bufferTime) {
                return redirect()->back()->withErrors(['time' => 'Услуга не помещается до перерыва']);
            }
        }

        // Проверка конфликтов
        if (Booking::where('schedule_id', $request->schedule_id)->where('status', 'confirmed')->exists()) {
            return redirect()->back()->withErrors(['time' => 'Время занято']);
        }

        // Проверка черного списка
        $client = Auth::guard('client')->user();
        if (Blacklist::where('master_id', $schedule->master_id)->where('client_email', $client->email)->exists()) {
            return redirect()->back()->withErrors(['client_email' => 'Вы в черном списке']);
        }

        Booking::create([
            'project_id' => $request->project_id,
            'client_id' => Auth::guard('client')->id(),
            'client_email' => Auth::guard('client')->user()->email,
            'service_id' => $request->service_id,
            'schedule_id' => $request->schedule_id,
            'status' => 'pending',
        ]);

        return redirect()->route('client.bookings')->with('message', 'Запись создана');
    }

    public function masterDashboard()
    {
        return view('master.dashboard');
    }

    public function masterBookings()
    {
        $bookings = Booking::whereIn('project_id', Auth::guard('master')->user()->projects->pluck('id'))
            ->with(['service', 'schedule'])
            ->get();
        return view('master.bookings', compact('bookings'));
    }

    public function createManual(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'service_id' => 'required|exists:services,id',
            'schedule_id' => 'required|exists:schedules,id',
            'client_email' => 'required|email',
            'client_name' => 'nullable|string',
        ]);

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
}
