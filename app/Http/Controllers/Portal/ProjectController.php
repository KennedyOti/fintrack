<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Project;
use App\Models\ProjectMilestone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $user      = Auth::user();
        $search    = $request->get('search');
        $status    = $request->get('status');
        $client_id = $request->get('client_id');

        $projects = Project::where('user_id', $user->id)
            ->with(['client'])
            ->withCount([
                'tasks',
                'tasks as completed_tasks_count' => fn($q) => $q->where('status', 'done'),
                'milestones',
                'milestones as completed_milestones_count' => fn($q) => $q->where('status', 'completed'),
            ])
            ->when($search, function ($query) use ($search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($status, fn($q) => $q->where('status', $status))
            ->when($client_id, fn($q) => $q->where('client_id', $client_id))
            ->latest()
            ->paginate(12);

        $clients = Client::where('user_id', $user->id)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        // Summary stats
        $allProjects = Project::where('user_id', $user->id);
        $stats = [
            'total'     => (clone $allProjects)->count(),
            'active'    => (clone $allProjects)->where('status', 'in_progress')->count(),
            'completed' => (clone $allProjects)->where('status', 'completed')->count(),
            'on_hold'   => (clone $allProjects)->where('status', 'on_hold')->count(),
        ];

        return view('portal.projects.index', compact('projects', 'clients', 'search', 'status', 'client_id', 'stats'));
    }

    public function create()
    {
        $clients = Client::where('user_id', Auth::id())
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('portal.projects.create', compact('clients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id'        => 'required|exists:clients,id',
            'title'            => 'required|string|max:200',
            'description'      => 'nullable|string',
            'budget'           => 'nullable|numeric|min:0',
            'start_date'       => 'required|date',
            'deadline'         => 'nullable|date|after_or_equal:start_date',
            'status'           => 'required|in:planned,in_progress,on_hold,completed,cancelled',
            'progress_percent' => 'required|integer|min:0|max:100',
        ]);

        $validated['user_id'] = Auth::id();

        if (empty($validated['budget']) && $validated['budget'] !== '0') {
            $validated['budget'] = 0;
        }

        $project = Project::create($validated);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Project created! Start by adding milestones and tasks.');
    }

    public function show(Project $project)
    {
        $this->authorizeProject($project);

        $project->load(['client']);

        // Milestones with task counts
        $milestones = $project->milestones()
            ->withCount([
                'tasks',
                'tasks as completed_tasks_count' => fn($q) => $q->where('status', 'done'),
            ])
            ->with(['tasks' => fn($q) => $q->orderBy('order_position')])
            ->orderBy('order_position')
            ->orderBy('due_date')
            ->get();

        // All tasks with milestones loaded
        $tasks = $project->tasks()
            ->with(['milestone'])
            ->orderBy('order_position')
            ->get();

        // Group by status for kanban
        $tasksByStatus = [
            'todo'        => $tasks->where('status', 'todo')->values(),
            'in_progress' => $tasks->where('status', 'in_progress')->values(),
            'in_review'   => $tasks->where('status', 'in_review')->values(),
            'blocked'     => $tasks->where('status', 'blocked')->values(),
            'done'        => $tasks->where('status', 'done')->values(),
        ];

        // Financial data
        $incomes  = $project->incomes()->with('category')->latest('income_date')->get();
        $expenses = $project->expenses()->with('category')->latest('expense_date')->get();
        $invoices = $project->invoices()->latest('issue_date')->get();

        // Stats
        $totalTasks         = $tasks->count();
        $completedTasks     = $tasks->where('status', 'done')->count();
        $totalMilestones    = $milestones->count();
        $completedMilestones = $milestones->where('status', 'completed')->count();
        $totalHoursLogged   = (float) $project->timeLogs()->sum('hours');
        $totalHoursEstimated = (float) $tasks->sum('estimated_hours');

        // Financial
        $totalIncome     = $project->totalIncome();
        $totalExpenses   = $project->totalExpenses();
        $profit          = $project->profit();
        $budgetUsedPercent = $project->budget > 0
            ? min(100, round(($totalExpenses / $project->budget) * 100))
            : 0;

        // Days remaining
        $daysRemaining = $project->daysRemaining();

        // Active tab (from redirect flash)
        $activeTab = session('active_tab', 'overview');

        return view('portal.projects.show', compact(
            'project', 'milestones', 'tasks', 'tasksByStatus',
            'incomes', 'expenses', 'invoices',
            'totalTasks', 'completedTasks',
            'totalMilestones', 'completedMilestones',
            'totalHoursLogged', 'totalHoursEstimated',
            'totalIncome', 'totalExpenses', 'profit', 'budgetUsedPercent',
            'daysRemaining', 'activeTab'
        ));
    }

    public function edit(Project $project)
    {
        $this->authorizeProject($project);

        $clients = Client::where('user_id', Auth::id())
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('portal.projects.edit', compact('project', 'clients'));
    }

    public function update(Request $request, Project $project)
    {
        $this->authorizeProject($project);

        $validated = $request->validate([
            'client_id'        => 'required|exists:clients,id',
            'title'            => 'required|string|max:200',
            'description'      => 'nullable|string',
            'budget'           => 'nullable|numeric|min:0',
            'start_date'       => 'required|date',
            'deadline'         => 'nullable|date|after_or_equal:start_date',
            'status'           => 'required|in:planned,in_progress,on_hold,completed,cancelled',
            'progress_percent' => 'required|integer|min:0|max:100',
        ]);

        if (empty($validated['budget']) && $validated['budget'] !== '0') {
            $validated['budget'] = 0;
        }

        $project->update($validated);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Project updated successfully.');
    }

    public function destroy(Project $project)
    {
        $this->authorizeProject($project);
        $project->delete();

        return redirect()->route('projects.index')
            ->with('success', 'Project deleted successfully.');
    }

    private function authorizeProject(Project $project): void
    {
        if ($project->user_id !== Auth::id()) {
            abort(403);
        }
    }
}
