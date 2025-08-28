<?php
namespace App\Http\Middleware;

use App\Models\Project;
use Closure;
use Illuminate\Http\Request;

class SpecialistPermission
{
    public function handle(Request $request, Closure $next, $permission)
    {
        $project = $request->route('project');
        $specialist = Auth::guard('specialist')->user();
        if (!$project->specialists()->where('specialist_id', $specialist->id)->exists()) {
            abort(403);
        }
        $pivot = $project->specialists()->where('specialist_id', $specialist->id)->first()->pivot;
        if (!$pivot->is_owner && !in_array($permission, $pivot->permissions ?? [])) {
            abort(403);
        }
        return $next($request);
    }
}
