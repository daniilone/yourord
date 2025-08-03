<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Master;
use App\Models\Client;
use App\Models\Project;
use App\Models\Tariff;
use App\Models\Payment;
use App\Models\DailyScheduleTemplate;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:web');
    }

    public function dashboard()
    {
        $users = User::count();
        $masters = Master::count();
        $clients = Client::count();
        $projects = Project::count();
        return view('admin.dashboard', compact('users', 'masters', 'clients', 'projects'));
    }

    public function users()
    {
        $users = User::all();
        return view('admin.users', compact('users'));
    }

    public function masters()
    {
        $masters = Master::with('projects')->get();
        return view('admin.masters', compact('masters'));
    }

    public function clients()
    {
        $clients = Client::with('bookings')->get();
        return view('admin.clients', compact('clients'));
    }

    public function projects()
    {
        $projects = Project::with(['master', 'categories', 'dailySchedules', 'dailyScheduleTemplates'])->get();
        return view('admin.projects', compact('projects'));
    }

    public function tariffs()
    {
        $tariffs = Tariff::with('masterTariffs')->get();
        return view('admin.tariffs', compact('tariffs'));
    }

    public function payments()
    {
        $payments = Payment::with(['master', 'tariff'])->get();
        return view('admin.payments', compact('payments'));
    }
}
