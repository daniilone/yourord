<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class SpecialistDashboardController extends Controller
{
    public function index()
    {
        $specialist = Auth::guard('specialist')->user();
        $projects = $specialist->projects()->get();
        return view('specialist.dashboard', compact('projects'));
    }
}
