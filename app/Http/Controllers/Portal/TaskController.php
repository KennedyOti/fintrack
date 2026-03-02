<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function create(Project $project)
    {
        $this->authorizeProject($project);

        $milestones        = $project->milestones()->orderBy('order_position')->get();
        $selectedMilestone = request('milestone_id');

        return view('portal.tasks.create', compact('project', 'milestones', 'selectedMilestone'));
    }

    public function store(Request $request, Project $project)
    {
        $this->authorizeProject($project);

        $validated = $request->validate([
            'title'           => 'required|string|max:200',
            'description'     => 'nullable|string',
            'milestone_id'    => 'nullable|exists:project_milestones,id',
            'priority'        => 'required|in:low,medium,high,critical',
            'status'          => 'required|in:todo,in_progress,in_review,blocked,done',
            'start_date'      => 'nullable|date',
            'due_date'        => 'nullable|date',
            'estimated_hours' => 'nullable|numeric|min:0|max:9999',
        ]);

        $validated['project_id']     = $project->id;
        $validated['order_position'] = $project->tasks()->where('status', $validated['status'])->count();

        if ($validated['status'] === 'done') {
            $validated['completed_at'] = now();
        }

        ProjectTask::create($validated);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Task created successfully.')
            ->with('active_tab', 'tasks');
    }

    public function edit(Project $project, ProjectTask $task)
    {
        $this->authorizeProject($project);

        $milestones = $project->milestones()->orderBy('order_position')->get();

        return view('portal.tasks.edit', compact('project', 'task', 'milestones'));
    }

    public function update(Request $request, Project $project, ProjectTask $task)
    {
        $this->authorizeProject($project);

        $validated = $request->validate([
            'title'           => 'required|string|max:200',
            'description'     => 'nullable|string',
            'milestone_id'    => 'nullable|exists:project_milestones,id',
            'priority'        => 'required|in:low,medium,high,critical',
            'status'          => 'required|in:todo,in_progress,in_review,blocked,done',
            'start_date'      => 'nullable|date',
            'due_date'        => 'nullable|date',
            'estimated_hours' => 'nullable|numeric|min:0|max:9999',
        ]);

        if ($validated['status'] === 'done' && $task->status !== 'done') {
            $validated['completed_at'] = now();
        } elseif ($validated['status'] !== 'done') {
            $validated['completed_at'] = null;
        }

        $task->update($validated);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Task updated successfully.')
            ->with('active_tab', 'tasks');
    }

    public function destroy(Project $project, ProjectTask $task)
    {
        $this->authorizeProject($project);
        $task->delete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('projects.show', $project)
            ->with('success', 'Task deleted.')
            ->with('active_tab', 'tasks');
    }

    public function updateStatus(Request $request, Project $project, ProjectTask $task)
    {
        $this->authorizeProject($project);

        $validated = $request->validate([
            'status' => 'required|in:todo,in_progress,in_review,blocked,done',
        ]);

        if ($validated['status'] === 'done' && $task->status !== 'done') {
            $validated['completed_at'] = now();
            $validated['actual_hours'] = $task->timeLogs()->sum('hours') ?: $task->actual_hours;
        } elseif ($validated['status'] !== 'done') {
            $validated['completed_at'] = null;
        }

        $task->update($validated);

        return response()->json([
            'success'     => true,
            'status'      => $task->status,
            'statusLabel' => $task->statusLabel(),
        ]);
    }

    public function reorder(Request $request, Project $project)
    {
        $this->authorizeProject($project);

        $validated = $request->validate([
            'tasks'          => 'required|array',
            'tasks.*.id'     => 'required|exists:project_tasks,id',
            'tasks.*.order'  => 'required|integer|min:0',
            'tasks.*.status' => 'required|in:todo,in_progress,in_review,blocked,done',
        ]);

        foreach ($validated['tasks'] as $item) {
            ProjectTask::where('id', $item['id'])
                ->where('project_id', $project->id)
                ->update([
                    'order_position' => $item['order'],
                    'status'         => $item['status'],
                ]);
        }

        return response()->json(['success' => true]);
    }

    private function authorizeProject(Project $project): void
    {
        if ($project->user_id !== Auth::id()) {
            abort(403);
        }
    }
}
