<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectTask;
use App\Models\ProjectTimeLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TimeLogController extends Controller
{
    public function store(Request $request, Project $project, ProjectTask $task)
    {
        if ($project->user_id !== Auth::id()) abort(403);

        $validated = $request->validate([
            'hours'       => 'required|numeric|min:0.1|max:24',
            'description' => 'nullable|string|max:500',
            'logged_date' => 'required|date|before_or_equal:today',
        ]);

        $validated['task_id']    = $task->id;
        $validated['project_id'] = $project->id;
        $validated['user_id']    = Auth::id();

        $log = ProjectTimeLog::create($validated);

        // Recalculate actual hours from all logs
        $task->update(['actual_hours' => $task->timeLogs()->sum('hours')]);

        return response()->json([
            'success'     => true,
            'log'         => $log,
            'total_hours' => (float) $task->fresh()->actual_hours,
        ]);
    }

    public function destroy(Project $project, ProjectTask $task, ProjectTimeLog $log)
    {
        if ($project->user_id !== Auth::id()) abort(403);

        $log->delete();

        // Recalculate actual hours
        $task->update(['actual_hours' => $task->timeLogs()->sum('hours')]);

        return response()->json([
            'success'     => true,
            'total_hours' => (float) $task->fresh()->actual_hours,
        ]);
    }
}
