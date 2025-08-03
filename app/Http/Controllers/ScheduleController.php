<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScheduleController extends Controller
{
    public function index()
    {
        $schedules = Schedule::where('master_id', Auth::guard('master')->id())->with('project')->get();
        return view('master.schedules', compact('schedules'));
    }

    public function store(Request $request)
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
