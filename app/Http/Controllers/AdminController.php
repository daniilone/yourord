<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Client;
use App\Models\Master;
use App\Models\Project;
use App\Models\Booking;
use App\Models\Tariff;
use App\Models\Payment;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'users' => User::count(),
            'clients' => Client::count(),
            'masters' => Master::count(),
            'projects' => Project::count(),
            'bookings' => Booking::count(),
            'tariffs' => Tariff::count(),
            'payments' => Payment::count(),
        ];
        return view('admin.dashboard', compact('stats'));
    }
}
