<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectMilestone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MilestoneController extends Controller
{
    public function store(Request $request, Project $project)
    {
        $this->authorizeProject($project);

        $validated = $request->validate([
            'title'          => 'required|string|max:200',
            'description'    => 'nullable|string',
            'priority'       => 'required|in:low,medium,high,critical',
            'status'         => 'required|in:pending,in_progress,completed',
            'start_date'     => 'nullable|date',
            'due_date'       => 'nullable|date',
            'amount'         => 'nullable|numeric|min:0',
            'color'          => 'nullable|string|max:7',
        ]);

        $validated['project_id']     = $project->id;
        $validated['order_position'] = $project->milestones()->count();

        ProjectMilestone::create($validated);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Milestone added successfully.')
            ->with('active_tab', 'milestones');
    }

    public function update(Request $request, Project $project, ProjectMilestone $milestone)
    {
        $this->authorizeProject($project);

        $validated = $request->validate([
            'title'          => 'required|string|max:200',
            'description'    => 'nullable|string',
            'priority'       => 'required|in:low,medium,high,critical',
            'status'         => 'required|in:pending,in_progress,completed',
            'start_date'     => 'nullable|date',
            'due_date'       => 'nullable|date',
            'amount'         => 'nullable|numeric|min:0',
            'color'          => 'nullable|string|max:7',
        ]);

        $milestone->update($validated);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Milestone updated successfully.')
            ->with('active_tab', 'milestones');
    }

    public function destroy(Project $project, ProjectMilestone $milestone)
    {
        $this->authorizeProject($project);

        // Unlink tasks before soft-deleting
        $milestone->tasks()->update(['milestone_id' => null]);
        $milestone->delete();

        return redirect()->route('projects.show', $project)
            ->with('success', 'Milestone deleted.')
            ->with('active_tab', 'milestones');
    }

    public function updateStatus(Request $request, Project $project, ProjectMilestone $milestone)
    {
        $this->authorizeProject($project);

        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,completed',
        ]);

        $milestone->update($validated);

        return response()->json(['success' => true, 'status' => $milestone->status, 'statusLabel' => $milestone->statusLabel()]);
    }

    private function authorizeProject(Project $project): void
    {
        if ($project->user_id !== Auth::id()) {
            abort(403);
        }
    }
}
