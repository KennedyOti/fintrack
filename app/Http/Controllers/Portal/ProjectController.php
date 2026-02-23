<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Client;
use App\Models\ProjectMilestone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $search = $request->get('search');
        $status = $request->get('status');
        $client_id = $request->get('client_id');
        
        $projects = Project::where('user_id', $user->id)
            ->with(['client'])
            ->when($search, function ($query) use ($search) {
                return $query->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })
            ->when($status, function ($query) use ($status) {
                return $query->where('status', $status);
            })
            ->when($client_id, function ($query) use ($client_id) {
                return $query->where('client_id', $client_id);
            })
            ->latest()
            ->paginate(10);
            
        $clients = Client::where('user_id', $user->id)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();
            
        return view('portal.projects.index', compact('projects', 'clients', 'search', 'status', 'client_id'));
    }

    public function create()
    {
        $user = Auth::user();
        
        $clients = Client::where('user_id', $user->id)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();
            
        return view('portal.projects.create', compact('clients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'title' => 'required|string|max:200',
            'description' => 'nullable|string',
            'budget' => 'nullable|numeric|min:0',
            'start_date' => 'required|date',
            'deadline' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:planned,in_progress,on_hold,completed,cancelled',
            'progress_percent' => 'required|integer|min:0|max:100',
        ], [
            'client_id.required' => 'Please select a client for this project.',
            'client_id.exists' => 'The selected client is invalid.',
            'title.required' => 'Please enter a project title.',
            'title.max' => 'The project title cannot exceed 200 characters.',
            'budget.numeric' => 'The budget must be a valid number.',
            'budget.min' => 'The budget cannot be negative.',
            'start_date.required' => 'Please select a start date for the project.',
            'start_date.date' => 'Please enter a valid start date.',
            'deadline.date' => 'Please enter a valid deadline date.',
            'deadline.after_or_equal' => 'The deadline must be on or after the start date.',
            'status.required' => 'Please select a project status.',
            'status.in' => 'The selected status is invalid.',
            'progress_percent.required' => 'Please enter the project progress percentage.',
            'progress_percent.integer' => 'Progress must be a whole number between 0 and 100.',
            'progress_percent.min' => 'Progress cannot be less than 0%.',
            'progress_percent.max' => 'Progress cannot exceed 100%.',
        ]);

        $validated['user_id'] = Auth::id();
        
        // Convert empty budget to 0 (default value in database)
        if (empty($validated['budget']) && $validated['budget'] !== '0') {
            $validated['budget'] = 0;
        }
        
        Project::create($validated);
        
        return redirect()->route('projects.index')
            ->with('success', 'Project created successfully.');
    }

    public function show(Project $project)
    {
        $this->authorizeProject($project);
        
        $project->load(['client', 'milestones', 'incomes', 'expenses']);
        
        return view('portal.projects.show', compact('project'));
    }

    public function edit(Project $project)
    {
        $this->authorizeProject($project);
        
        $user = Auth::user();
        
        $clients = Client::where('user_id', $user->id)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();
            
        return view('portal.projects.edit', compact('project', 'clients'));
    }

    public function update(Request $request, Project $project)
    {
        $this->authorizeProject($project);
        
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'title' => 'required|string|max:200',
            'description' => 'nullable|string',
            'budget' => 'nullable|numeric|min:0',
            'start_date' => 'required|date',
            'deadline' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:planned,in_progress,on_hold,completed,cancelled',
            'progress_percent' => 'required|integer|min:0|max:100',
        ], [
            'client_id.required' => 'Please select a client for this project.',
            'client_id.exists' => 'The selected client is invalid.',
            'title.required' => 'Please enter a project title.',
            'title.max' => 'The project title cannot exceed 200 characters.',
            'budget.numeric' => 'The budget must be a valid number.',
            'budget.min' => 'The budget cannot be negative.',
            'start_date.required' => 'Please select a start date for the project.',
            'start_date.date' => 'Please enter a valid start date.',
            'deadline.date' => 'Please enter a valid deadline date.',
            'deadline.after_or_equal' => 'The deadline must be on or after the start date.',
            'status.required' => 'Please select a project status.',
            'status.in' => 'The selected status is invalid.',
            'progress_percent.required' => 'Please enter the project progress percentage.',
            'progress_percent.integer' => 'Progress must be a whole number between 0 and 100.',
            'progress_percent.min' => 'Progress cannot be less than 0%.',
            'progress_percent.max' => 'Progress cannot exceed 100%.',
        ]);

        // Convert empty budget to 0 (default value in database)
        if (empty($validated['budget']) && $validated['budget'] !== '0') {
            $validated['budget'] = 0;
        }
        
        $project->update($validated);
        
        return redirect()->route('projects.index')
            ->with('success', 'Project updated successfully.');
    }

    public function destroy(Project $project)
    {
        $this->authorizeProject($project);
        
        $project->delete();
        
        return redirect()->route('projects.index')
            ->with('success', 'Project deleted successfully.');
    }

    private function authorizeProject($project)
    {
        if ($project->user_id !== Auth::id()) {
            abort(403);
        }
    }
}
