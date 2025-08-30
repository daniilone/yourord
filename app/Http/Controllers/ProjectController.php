<?php
namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Specialist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    public function index()
    {
        $specialist = Auth::guard('specialist')->user();
        $projects = $specialist->projects()->get();
        return view('specialist.projects', compact('projects'));
    }

    public function create()
    {
        return view('specialist.projects.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'client_id' => 'required|exists:clients,id',
        ]);

        $project = Project::create([
            'title' => $request->title,
            'description' => $request->description,
            'client_id' => $request->client_id,
        ]);

        // Привязываем проект к специалисту
        $specialist = Auth::guard('specialist')->user();
        $project->specialists()->attach($specialist->id, [
            'is_owner' => true,
            'permissions' => json_encode(['manage_tasks', 'view_details']),
        ]);

        return redirect()->route('specialist.projects')->with('success', 'Проект успешно создан.');
    }
}
