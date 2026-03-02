@extends('layouts.portal')

@section('title', $project->title)

@section('styles')
<style>
/* ── Project Show ─────────────────────────────────────── */
:root {
    --pr-critical:#F43F5E; --pr-high:#F59E0B; --pr-medium:#0E7490; --pr-low:#6B7280;
    --ts-todo:#94A3B8; --ts-progress:#3B82F6; --ts-review:#8B5CF6; --ts-blocked:#F43F5E; --ts-done:#22C55E;
}
/* Hero header */
.proj-hero {
    border-radius: 14px;
    padding: 1.5rem;
    border: 1px solid var(--border-color, rgba(255,255,255,0.08));
    background: var(--card-bg, #fff);
    margin-bottom: 1.5rem;
    position: relative;
    overflow: hidden;
}
.proj-hero::before {
    content:''; position: absolute; top:0; left:0; right:0; height:4px;
    background: linear-gradient(90deg, var(--proj-color, #0E7490), transparent);
}
.proj-hero-avatar {
    width: 52px; height: 52px; border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.4rem; font-weight: 800; color: #fff; flex-shrink: 0;
}
/* Quick stat cards */
.qs-card {
    border-radius: 12px; padding: 1.1rem 1.25rem;
    background: var(--card-bg, #fff); border: 1px solid var(--border-color, rgba(255,255,255,0.08));
}
.qs-num  { font-size: 1.6rem; font-weight: 800; line-height: 1; }
.qs-lbl  { font-size: 0.73rem; opacity: 0.6; margin-top: 3px; }
.qs-icon { width: 38px; height: 38px; border-radius: 9px; display: flex; align-items: center; justify-content: center; font-size: 1rem; flex-shrink: 0; }

/* Tabs */
.proj-tabs { border-bottom: 2px solid var(--border-color, rgba(255,255,255,0.08)); margin-bottom: 1.5rem; display: flex; gap: 0; overflow-x: auto; }
.proj-tab {
    padding: 0.7rem 1.2rem; font-size: 0.82rem; font-weight: 600;
    border: none; background: transparent; cursor: pointer;
    border-bottom: 2px solid transparent; margin-bottom: -2px;
    white-space: nowrap; transition: all 0.15s;
}
.proj-tab.active { border-bottom-color: var(--ft-teal, #0E7490); color: var(--ft-teal, #0E7490); }
.tab-badge { border-radius: 99px; padding: 1px 7px; font-size: 0.7rem; margin-left: 5px; }

/* Milestone cards */
.milestone-card {
    border-radius: 10px; border: 1px solid var(--border-color, rgba(255,255,255,0.08));
    overflow: hidden; background: var(--card-bg, #fff); margin-bottom: 0.75rem;
    border-left: 3px solid;
}
.milestone-header { padding: 1rem 1.25rem; display: flex; align-items: center; gap: 0.75rem; cursor: pointer; transition: background 0.15s; }
.milestone-progress-bar { height: 5px; border-radius: 99px; overflow: hidden; flex: 1; min-width: 60px; }
.milestone-progress-fill { height: 100%; border-radius: 99px; transition: width 0.4s; }
.milestone-tasks { padding: 0 1.25rem 1rem; border-top: 1px solid var(--border-color, rgba(255,255,255,0.06)); }
.milestone-task-row { display: flex; align-items: center; gap: 0.6rem; padding: 0.5rem 0; border-bottom: 1px solid var(--border-color, rgba(255,255,255,0.05)); }
.milestone-task-row:last-child { border-bottom: none; }
.task-check {
    width: 18px; height: 18px; border-radius: 50%; border: 2px solid var(--border-color, rgba(255,255,255,0.2));
    display: flex; align-items: center; justify-content: center; cursor: pointer;
    flex-shrink: 0; transition: all 0.2s;
}
.task-check.done { background: #22C55E; border-color: #22C55E; }
.task-check i { font-size: 0.55rem; color: #fff; display: none; }
.task-check.done i { display: block; }

/* Priority badges */
.bdg-priority-critical { background: rgba(244,63,94,0.14); color:#F43F5E; }
.bdg-priority-high     { background: rgba(245,158,11,0.14); color:#C2800A; }
.bdg-priority-medium   { background: rgba(14,116,144,0.14); color:#0E7490; }
.bdg-priority-low      { background: rgba(107,114,128,0.14);color:#4B5563; }
/* Status badges */
.bdg-todo     { background:rgba(148,163,184,0.18);color:#475569; }
.bdg-progress { background:rgba(59,130,246,0.14); color:#2563EB; }
.bdg-review   { background:rgba(139,92,246,0.14); color:#7C3AED; }
.bdg-blocked  { background:rgba(244,63,94,0.14);  color:#E11D48; }
.bdg-done     { background:rgba(34,197,94,0.14);  color:#15803D; }

/* Task list */
.task-list-row {
    display: flex; align-items: center; gap: 0.75rem;
    padding: 0.75rem 1rem; border-radius: 8px; margin-bottom: 0.35rem;
    background: var(--card-bg, #fff); border: 1px solid var(--border-color, rgba(255,255,255,0.06));
    transition: background 0.15s, border-color 0.15s;
}
.task-list-row.done-task { opacity: 0.55; }
.task-list-row.done-task .task-title-text { text-decoration: line-through; }
.priority-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }

/* Kanban */
.kanban-board { display: flex; gap: 1rem; overflow-x: auto; padding-bottom: 1rem; min-height: 500px; }
.kanban-col { min-width: 220px; flex: 1; display: flex; flex-direction: column; }
.kanban-col-header {
    padding: 0.65rem 0.9rem; border-radius: 8px 8px 0 0;
    font-weight: 700; font-size: 0.78rem; letter-spacing: 0.5px;
    display: flex; align-items: center; justify-content: space-between;
    border-bottom: 2px solid;
}
.kanban-tasks { flex: 1; padding: 0.5rem; border-radius: 0 0 8px 8px; min-height: 200px; background: rgba(0,0,0,0.04); }
.kanban-card {
    background: var(--card-bg, #fff); border: 1px solid var(--border-color, rgba(255,255,255,0.08));
    border-left: 3px solid; border-radius: 8px; padding: 0.7rem 0.8rem;
    margin-bottom: 0.5rem; cursor: grab; user-select: none;
    transition: box-shadow 0.2s, transform 0.15s;
}
.kanban-card.sortable-ghost { opacity: 0.35; }
.kanban-card.sortable-chosen { cursor: grabbing; box-shadow: 0 8px 24px rgba(0,0,0,0.25); transform: rotate(1deg); }
.kanban-card-title { font-weight: 600; font-size: 0.82rem; line-height: 1.35; margin-bottom: 0.45rem; color: var(--text-h, #0F172A); }
.kanban-card-meta { font-size: 0.7rem; opacity: 0.65; display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap; }
.kanban-add-btn { width: 100%; padding: 0.45rem; border-radius: 6px; border: 1px dashed; background: transparent; font-size: 0.78rem; cursor: pointer; transition: all 0.15s; margin-top: 0.5rem; display: flex; align-items: center; justify-content: center; gap: 4px; }

/* Time log modal */
.time-log-item { display: flex; align-items: flex-start; gap: 0.6rem; padding: 0.5rem 0; border-bottom: 1px solid var(--border-color, rgba(255,255,255,0.06)); }
.time-log-item:last-child { border-bottom: none; }
</style>
@endsection

@section('content')
@php
    $statusColors = [
        'planned'=>'#94A3B8','in_progress'=>'#0E7490',
        'on_hold'=>'#F59E0B','completed'=>'#22C55E','cancelled'=>'#F43F5E',
    ];
    $sc = $statusColors[$project->status] ?? '#94A3B8';
    $user = auth()->user();
    $currency = $user->currency_code ?? 'USD';
    $symbol = \App\Helpers\CurrencyHelper::getSymbol($currency);
@endphp

{{-- Project Hero Header --}}
<div class="proj-hero" style="--proj-color:{{ $sc }}">
    <div class="d-flex align-items-start gap-3 flex-wrap">
        <div class="proj-hero-avatar" style="background:{{ $sc }}">{{ strtoupper(substr($project->title,0,1)) }}</div>
        <div class="flex-fill">
            <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                <h2 class="fw-800 mb-0" style="font-size:1.35rem">{{ $project->title }}</h2>
                @php
                    $slMap = ['planned'=>['Planned','rgba(148,163,184,0.15)','#94A3B8'],'in_progress'=>['In Progress','rgba(14,116,144,0.15)','#0E7490'],'on_hold'=>['On Hold','rgba(245,158,11,0.15)','#F59E0B'],'completed'=>['Completed','rgba(34,197,94,0.15)','#22C55E'],'cancelled'=>['Cancelled','rgba(244,63,94,0.15)','#F43F5E']];
                    $sl = $slMap[$project->status] ?? $slMap['planned'];
                @endphp
                <span class="badge" style="background:{{ $sl[1] }};color:{{ $sl[2] }}">{{ $sl[0] }}</span>
                @if($project->isOverdue())
                    <span class="badge" style="background:rgba(244,63,94,0.12);color:#F43F5E"><i class="fas fa-exclamation-triangle me-1"></i>Overdue</span>
                @endif
            </div>
            <div class="d-flex align-items-center gap-3 flex-wrap" style="font-size:0.82rem;opacity:0.7">
                @if($project->client)
                    <span><i class="fas fa-user me-1"></i>{{ $project->client->name }}</span>
                @endif
                @if($project->start_date)
                    <span><i class="fas fa-play-circle me-1"></i>{{ $project->start_date->format('M d, Y') }}</span>
                @endif
                @if($project->deadline)
                    <span><i class="fas fa-flag-checkered me-1"></i>{{ $project->deadline->format('M d, Y') }}
                        @if($daysRemaining !== null)
                            <span class="ms-1 fw-600" style="color:{{ $daysRemaining < 0 ? '#F43F5E' : ($daysRemaining <= 7 ? '#F59E0B' : '#22C55E') }}">
                                ({{ $daysRemaining < 0 ? abs($daysRemaining).'d overdue' : $daysRemaining.'d left' }})
                            </span>
                        @endif
                    </span>
                @endif
            </div>
        </div>
        <div class="d-flex gap-2 flex-shrink-0">
            <a href="{{ route('projects.edit', $project) }}" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-pen me-1"></i>Edit
            </a>
            <a href="{{ route('projects.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back
            </a>
        </div>
    </div>

    {{-- Project-wide progress bar --}}
    <div class="mt-3">
        <div class="d-flex justify-content-between mb-1" style="font-size:0.75rem;opacity:0.75">
            <span>Overall Progress</span>
            <span class="fw-700">{{ $project->progress_percent }}%</span>
        </div>
        <div style="height:8px;background:rgba(128,128,128,0.15);border-radius:99px;overflow:hidden">
            <div style="height:100%;width:{{ $project->progress_percent }}%;background:{{ $sc }};border-radius:99px;transition:width 0.5s"></div>
        </div>
    </div>
</div>

{{-- Quick Stats --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="qs-card">
            <div class="d-flex align-items-center gap-3">
                <div class="qs-icon" style="background:rgba(14,116,144,0.12);color:#0E7490"><i class="fas fa-tasks"></i></div>
                <div>
                    <div class="qs-num">{{ $completedTasks }}<span style="font-size:1rem;opacity:0.5">/{{ $totalTasks }}</span></div>
                    <div class="qs-lbl">Tasks Done</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="qs-card">
            <div class="d-flex align-items-center gap-3">
                <div class="qs-icon" style="background:rgba(139,92,246,0.12);color:#8B5CF6"><i class="fas fa-flag"></i></div>
                <div>
                    <div class="qs-num">{{ $completedMilestones }}<span style="font-size:1rem;opacity:0.5">/{{ $totalMilestones }}</span></div>
                    <div class="qs-lbl">Milestones</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="qs-card">
            <div class="d-flex align-items-center gap-3">
                <div class="qs-icon" style="background:rgba(34,197,94,0.12);color:#22C55E"><i class="fas fa-clock"></i></div>
                <div>
                    <div class="qs-num">{{ number_format($totalHoursLogged,1) }}<span style="font-size:0.85rem;opacity:0.5">h</span></div>
                    <div class="qs-lbl">Hours Logged{{ $totalHoursEstimated > 0 ? ' / '.number_format($totalHoursEstimated,0).'h est.' : '' }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="qs-card">
            <div class="d-flex align-items-center gap-3">
                <div class="qs-icon" style="background:rgba(245,158,11,0.12);color:#F59E0B"><i class="fas fa-wallet"></i></div>
                <div>
                    @if($project->budget > 0)
                        <div class="qs-num">{{ $budgetUsedPercent }}%</div>
                        <div class="qs-lbl">Budget Used ({{ $symbol }}{{ number_format($totalExpenses,0) }})</div>
                    @else
                        <div class="qs-num">{{ $symbol }}{{ number_format($totalIncome,0) }}</div>
                        <div class="qs-lbl">Total Income</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Tab Navigation --}}
<div class="proj-tabs" id="projTabs">
    <button class="proj-tab" data-tab="overview">
        <i class="fas fa-th-large me-1"></i>Overview
    </button>
    <button class="proj-tab" data-tab="milestones">
        <i class="fas fa-flag me-1"></i>Milestones
        <span class="tab-badge">{{ $totalMilestones }}</span>
    </button>
    <button class="proj-tab" data-tab="tasks">
        <i class="fas fa-list-check me-1"></i>Tasks
        <span class="tab-badge">{{ $totalTasks }}</span>
    </button>
    <button class="proj-tab" data-tab="kanban">
        <i class="fas fa-columns me-1"></i>Kanban
    </button>
    <button class="proj-tab" data-tab="financials">
        <i class="fas fa-chart-bar me-1"></i>Financials
    </button>
</div>

{{-- ─────────────── TAB: OVERVIEW ─────────────── --}}
<div class="tab-content" id="tab-overview">
    <div class="row g-3">
        <div class="col-lg-8">
            {{-- Description --}}
            @if($project->description)
            <div class="card mb-3">
                <div class="card-body">
                    <h6 class="fw-700 mb-2"><i class="fas fa-align-left me-2 opacity-50"></i>Description</h6>
                    <p class="mb-0" style="font-size:0.88rem;line-height:1.7">{{ $project->description }}</p>
                </div>
            </div>
            @endif

            {{-- Upcoming Milestones --}}
            @if($milestones->isNotEmpty())
            <div class="card mb-3">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h6 class="fw-700 mb-0"><i class="fas fa-flag me-2" style="color:#8B5CF6"></i>Milestones</h6>
                        <button class="btn btn-xs btn-outline-secondary" onclick="switchTab('milestones')">View All</button>
                    </div>
                    @foreach($milestones->take(4) as $ms)
                    @php
                        $msTotal = $ms->tasks_count ?? 0;
                        $msDone  = $ms->completed_tasks_count ?? 0;
                        $msPct   = $msTotal > 0 ? round(($msDone/$msTotal)*100) : 0;
                    @endphp
                    <div class="d-flex align-items-center gap-3 mb-2">
                        <div style="width:10px;height:10px;border-radius:50%;background:{{ $ms->displayColor() }};flex-shrink:0"></div>
                        <div class="flex-fill" style="min-width:0">
                            <div class="d-flex justify-content-between" style="font-size:0.82rem">
                                <span class="fw-600 text-truncate">{{ $ms->title }}</span>
                                <span class="opacity-65 ms-2">{{ $msDone }}/{{ $msTotal }}</span>
                            </div>
                            <div style="height:4px;background:rgba(128,128,128,0.15);border-radius:99px;margin-top:4px">
                                <div style="height:100%;width:{{ $msPct }}%;background:{{ $ms->displayColor() }};border-radius:99px"></div>
                            </div>
                        </div>
                        <span class="badge" style="font-size:0.65rem;background:{{ $ms->status === 'completed' ? 'rgba(34,197,94,0.12)' : ($ms->status === 'in_progress' ? 'rgba(14,116,144,0.12)' : 'rgba(148,163,184,0.12)') }};color:{{ $ms->statusColor() }}">{{ $ms->statusLabel() }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Recent Tasks --}}
            @if($tasks->isNotEmpty())
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h6 class="fw-700 mb-0"><i class="fas fa-tasks me-2" style="color:#0E7490"></i>Recent Tasks</h6>
                        <button class="btn btn-xs btn-outline-secondary" onclick="switchTab('tasks')">View All</button>
                    </div>
                    @foreach($tasks->sortByDesc('updated_at')->take(5) as $task)
                    <div class="task-list-row {{ $task->status === 'done' ? 'done-task' : '' }}" style="margin-bottom:0.3rem">
                        <div class="priority-dot" style="background:{{ $task->priorityColor() }}"></div>
                        <div class="flex-fill" style="min-width:0">
                            <div class="task-title-text fw-600" style="font-size:0.82rem">{{ $task->title }}</div>
                            @if($task->milestone)
                                <div style="font-size:0.7rem;opacity:0.55"><i class="fas fa-flag me-1"></i>{{ $task->milestone->title }}</div>
                            @endif
                        </div>
                        <span class="badge {{ $task->statusBadgeClass() }}" style="font-size:0.65rem">{{ $task->statusLabel() }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
        <div class="col-lg-4">
            <div class="card mb-3">
                <div class="card-body">
                    <h6 class="fw-700 mb-3"><i class="fas fa-info-circle me-2 opacity-50"></i>Project Info</h6>
                    <dl style="font-size:0.82rem">
                        @if($project->client)
                        <div class="d-flex justify-content-between mb-2">
                            <dt class="fw-normal opacity-65">Client</dt>
                            <dd class="mb-0 text-end fw-600">{{ $project->client->name }}</dd>
                        </div>
                        @endif
                        <div class="d-flex justify-content-between mb-2">
                            <dt class="fw-normal opacity-65">Status</dt>
                            <dd class="mb-0 text-end"><span class="badge" style="background:{{ $sl[1] }};color:{{ $sl[2] }}">{{ $sl[0] }}</span></dd>
                        </div>
                        @if($project->start_date)
                        <div class="d-flex justify-content-between mb-2">
                            <dt class="fw-normal opacity-65">Start Date</dt>
                            <dd class="mb-0 text-end fw-600">{{ $project->start_date->format('M d, Y') }}</dd>
                        </div>
                        @endif
                        @if($project->deadline)
                        <div class="d-flex justify-content-between mb-2">
                            <dt class="fw-normal opacity-65">Deadline</dt>
                            <dd class="mb-0 text-end fw-600">{{ $project->deadline->format('M d, Y') }}</dd>
                        </div>
                        @endif
                        @if($project->budget > 0)
                        <div class="d-flex justify-content-between mb-2">
                            <dt class="fw-normal opacity-65">Budget</dt>
                            <dd class="mb-0 text-end fw-600">{{ $symbol }}{{ number_format($project->budget,2) }}</dd>
                        </div>
                        @endif
                        <div class="d-flex justify-content-between">
                            <dt class="fw-normal opacity-65">Created</dt>
                            <dd class="mb-0 text-end fw-600">{{ $project->created_at->format('M d, Y') }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <h6 class="fw-700 mb-3"><i class="fas fa-bolt me-2 opacity-50"></i>Quick Actions</h6>
                    <div class="d-grid gap-2">
                        <a href="{{ route('projects.tasks.create', $project) }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-plus me-1"></i>Add Task
                        </a>
                        <button class="btn btn-sm btn-outline-secondary" onclick="switchTab('milestones');document.getElementById('addMilestoneBtn').click()">
                            <i class="fas fa-flag me-1"></i>Add Milestone
                        </button>
                        <a href="{{ route('invoices.create') }}?project_id={{ $project->id }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-file-invoice me-1"></i>Create Invoice
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ─────────────── TAB: MILESTONES ─────────────── --}}
<div class="tab-content" id="tab-milestones" style="display:none">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <span class="fw-700">{{ $totalMilestones }} milestone{{ $totalMilestones != 1 ? 's' : '' }}</span>
            <span class="opacity-65 ms-2" style="font-size:0.82rem">{{ $completedMilestones }} completed</span>
        </div>
        <button class="btn btn-sm btn-primary" id="addMilestoneBtn" data-bs-toggle="modal" data-bs-target="#milestoneModal">
            <i class="fas fa-plus me-1"></i>Add Milestone
        </button>
    </div>

    @if($milestones->isEmpty())
        <div class="text-center py-5 opacity-65">
            <i class="fas fa-flag fa-2x mb-3 d-block" style="color:#8B5CF6"></i>
            <p class="mb-2 fw-600">No milestones yet</p>
            <p class="small">Break your project into phases — design, development, launch, etc.</p>
        </div>
    @else
        @foreach($milestones as $ms)
        @php
            $msTotal = $ms->tasks_count ?? 0;
            $msDone  = $ms->completed_tasks_count ?? 0;
            $msPct   = $msTotal > 0 ? round(($msDone/$msTotal)*100) : 0;
        @endphp
        <div class="milestone-card" style="border-left-color:{{ $ms->displayColor() }}" id="ms-{{ $ms->id }}">
            <div class="milestone-header" onclick="toggleMilestone({{ $ms->id }})">
                <div style="width:12px;height:12px;border-radius:50%;background:{{ $ms->displayColor() }};flex-shrink:0"></div>
                <div class="flex-fill" style="min-width:0">
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <span class="fw-700" style="font-size:0.9rem">{{ $ms->title }}</span>
                        <span class="badge {{ $ms->priorityBadgeClass() }}" style="font-size:0.65rem">{{ ucfirst($ms->priority) }}</span>
                        <span class="badge" style="font-size:0.65rem;background:{{ $ms->status==='completed'?'rgba(34,197,94,0.12)':($ms->status==='in_progress'?'rgba(14,116,144,0.12)':'rgba(148,163,184,0.12)') }};color:{{ $ms->statusColor() }}">{{ $ms->statusLabel() }}</span>
                        @if($ms->isOverdue())<span class="badge" style="font-size:0.65rem;background:rgba(244,63,94,0.12);color:#F43F5E">Overdue</span>@endif
                    </div>
                    <div class="d-flex align-items-center gap-3 mt-1 flex-wrap" style="font-size:0.73rem;opacity:0.6">
                        @if($ms->start_date)<span><i class="fas fa-play me-1"></i>{{ $ms->start_date->format('M d') }}</span>@endif
                        @if($ms->due_date)<span><i class="fas fa-calendar me-1"></i>{{ $ms->due_date->format('M d, Y') }}</span>@endif
                        @if($ms->amount > 0)<span><i class="fas fa-dollar-sign me-1"></i>{{ $symbol }}{{ number_format($ms->amount,2) }}</span>@endif
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <div class="text-end" style="min-width:80px">
                        <div style="font-size:0.7rem;opacity:0.6;margin-bottom:3px">{{ $msDone }}/{{ $msTotal }} tasks</div>
                        <div class="milestone-progress-bar" style="width:80px">
                            <div class="milestone-progress-fill" style="width:{{ $msPct }}%;background:{{ $ms->displayColor() }}"></div>
                        </div>
                    </div>
                    <div class="d-flex gap-1" onclick="event.stopPropagation()">
                        <button class="btn btn-xs btn-outline-secondary"
                            onclick="editMilestone({{ $ms->id }}, {{ json_encode($ms->title) }}, {{ json_encode($ms->description) }}, '{{ $ms->priority }}', '{{ $ms->status }}', '{{ $ms->start_date?->format('Y-m-d') }}', '{{ $ms->due_date?->format('Y-m-d') }}', '{{ $ms->amount }}', '{{ $ms->color ?? '' }}')"
                            title="Edit">
                            <i class="fas fa-pen"></i>
                        </button>
                        <button class="btn btn-xs btn-outline-danger" onclick="deleteMilestone({{ $ms->id }}, {{ json_encode($ms->title) }})" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                    <i class="fas fa-chevron-down ms-1" id="ms-chevron-{{ $ms->id }}" style="opacity:0.4;font-size:0.75rem;transition:transform 0.2s"></i>
                </div>
            </div>

            <div class="milestone-tasks" id="ms-tasks-{{ $ms->id }}" style="display:none">
                @if($ms->tasks->isEmpty())
                    <div class="py-2 text-center" style="font-size:0.8rem;opacity:0.55">No tasks in this milestone yet.</div>
                @else
                    @foreach($ms->tasks as $task)
                    <div class="milestone-task-row" id="ms-task-{{ $task->id }}">
                        <div class="task-check {{ $task->status === 'done' ? 'done' : '' }}"
                             onclick="quickToggleTask({{ $project->id }}, {{ $task->id }}, this)" title="Mark done">
                            <i class="fas fa-check"></i>
                        </div>
                        <div class="priority-dot" style="background:{{ $task->priorityColor() }}"></div>
                        <div class="flex-fill" style="min-width:0">
                            <span class="fw-600" style="font-size:0.82rem;{{ $task->status==='done'?'text-decoration:line-through;opacity:0.6':'' }}">{{ $task->title }}</span>
                        </div>
                        @if($task->due_date)
                            <span style="font-size:0.7rem;opacity:0.55"><i class="fas fa-calendar me-1"></i>{{ $task->due_date->format('M d') }}</span>
                        @endif
                        <span class="badge {{ $task->statusBadgeClass() }}" style="font-size:0.63rem">{{ $task->statusLabel() }}</span>
                        <div class="d-flex gap-1">
                            <a href="{{ route('projects.tasks.edit', [$project, $task]) }}" class="btn btn-xs btn-outline-secondary"><i class="fas fa-pen"></i></a>
                            <button class="btn btn-xs btn-outline-danger" onclick="deleteTask({{ $project->id }}, {{ $task->id }})"><i class="fas fa-trash"></i></button>
                        </div>
                    </div>
                    @endforeach
                @endif
                <div class="pt-2">
                    <a href="{{ route('projects.tasks.create', $project) }}?milestone_id={{ $ms->id }}"
                       class="btn btn-xs btn-outline-secondary">
                        <i class="fas fa-plus me-1"></i>Add task to this milestone
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    @endif
</div>

{{-- ─────────────── TAB: TASKS (LIST) ─────────────── --}}
<div class="tab-content" id="tab-tasks" style="display:none">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <span class="fw-700">{{ $totalTasks }} task{{ $totalTasks != 1 ? 's' : '' }}</span>
            <span class="opacity-65 ms-1" style="font-size:0.82rem">{{ $completedTasks }} done</span>
            {{-- Filter by status --}}
            <div class="d-flex gap-1 ms-2" id="taskStatusFilter">
                <button class="btn btn-xs btn-outline-secondary filter-btn active" data-filter="all">All</button>
                <button class="btn btn-xs btn-outline-secondary filter-btn" data-filter="todo" style="color:#94A3B8">To Do</button>
                <button class="btn btn-xs btn-outline-secondary filter-btn" data-filter="in_progress" style="color:#3B82F6">In Progress</button>
                <button class="btn btn-xs btn-outline-secondary filter-btn" data-filter="blocked" style="color:#F43F5E">Blocked</button>
                <button class="btn btn-xs btn-outline-secondary filter-btn" data-filter="done" style="color:#22C55E">Done</button>
            </div>
        </div>
        <a href="{{ route('projects.tasks.create', $project) }}" class="btn btn-sm btn-primary">
            <i class="fas fa-plus me-1"></i>Add Task
        </a>
    </div>

    @if($tasks->isEmpty())
        <div class="text-center py-5 opacity-65">
            <i class="fas fa-tasks fa-2x mb-3 d-block" style="color:#0E7490"></i>
            <p class="mb-2 fw-600">No tasks yet</p>
            <p class="small">Add tasks to break down work into actionable steps.</p>
            <a href="{{ route('projects.tasks.create', $project) }}" class="btn btn-sm btn-primary mt-2">
                <i class="fas fa-plus me-1"></i>Add First Task
            </a>
        </div>
    @else
        <div id="taskListContainer">
            @foreach($tasks->sortBy('order_position') as $task)
            @php
                $prioColors = ['critical'=>'#F43F5E','high'=>'#F59E0B','medium'=>'#0E7490','low'=>'#6B7280'];
                $pc = $prioColors[$task->priority] ?? '#6B7280';
            @endphp
            <div class="task-list-row {{ $task->status === 'done' ? 'done-task' : '' }}" data-status="{{ $task->status }}">
                <div class="task-check {{ $task->status === 'done' ? 'done' : '' }}"
                     onclick="quickToggleTask({{ $project->id }}, {{ $task->id }}, this)">
                    <i class="fas fa-check"></i>
                </div>
                <div class="priority-dot" style="background:{{ $pc }}"></div>
                <div class="flex-fill" style="min-width:0">
                    <div class="d-flex align-items-center gap-2">
                        <span class="task-title-text fw-600" style="font-size:0.85rem">{{ $task->title }}</span>
                        <span class="badge {{ $task->priorityBadgeClass() }}" style="font-size:0.63rem">{{ $task->priorityLabel() }}</span>
                    </div>
                    <div class="d-flex align-items-center gap-3 mt-1 flex-wrap" style="font-size:0.72rem;opacity:0.6">
                        @if($task->milestone)
                            <span><i class="fas fa-flag me-1"></i>{{ $task->milestone->title }}</span>
                        @endif
                        @if($task->due_date)
                            <span class="{{ $task->isOverdue() ? '' : '' }}" style="{{ $task->isOverdue() ? 'color:#F43F5E' : '' }}">
                                <i class="fas fa-calendar me-1"></i>{{ $task->due_date->format('M d') }}
                                @if($task->isOverdue()) <i class="fas fa-exclamation-circle ms-1"></i>@endif
                            </span>
                        @endif
                        @if($task->estimated_hours)
                            <span><i class="fas fa-clock me-1"></i>{{ $task->actual_hours }}h / {{ $task->estimated_hours }}h</span>
                        @endif
                    </div>
                </div>
                <span class="badge {{ $task->statusBadgeClass() }}" style="font-size:0.7rem">{{ $task->statusLabel() }}</span>
                <div class="d-flex gap-1">
                    <button class="btn btn-xs btn-outline-secondary" onclick="openTimeLog({{ $task->id }}, {{ json_encode($task->title) }}, {{ $project->id }})" title="Log Time">
                        <i class="fas fa-clock"></i>
                    </button>
                    <a href="{{ route('projects.tasks.edit', [$project, $task]) }}" class="btn btn-xs btn-outline-secondary" title="Edit">
                        <i class="fas fa-pen"></i>
                    </a>
                    <button class="btn btn-xs btn-outline-danger" onclick="deleteTask({{ $project->id }}, {{ $task->id }})" title="Delete">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>

{{-- ─────────────── TAB: KANBAN ─────────────── --}}
<div class="tab-content" id="tab-kanban" style="display:none">
    @php
        $kanbanCols = [
            'todo'        => ['label'=>'To Do',       'color'=>'#94A3B8','bg'=>'rgba(148,163,184,0.1)'],
            'in_progress' => ['label'=>'In Progress',  'color'=>'#3B82F6','bg'=>'rgba(59,130,246,0.1)'],
            'in_review'   => ['label'=>'In Review',    'color'=>'#8B5CF6','bg'=>'rgba(139,92,246,0.1)'],
            'blocked'     => ['label'=>'Blocked',      'color'=>'#F43F5E','bg'=>'rgba(244,63,94,0.1)'],
            'done'        => ['label'=>'Done',         'color'=>'#22C55E','bg'=>'rgba(34,197,94,0.1)'],
        ];
        $prioColors = ['critical'=>'#F43F5E','high'=>'#F59E0B','medium'=>'#0E7490','low'=>'#6B7280'];
    @endphp
    <div class="d-flex justify-content-end mb-3">
        <a href="{{ route('projects.tasks.create', $project) }}" class="btn btn-sm btn-primary">
            <i class="fas fa-plus me-1"></i>Add Task
        </a>
    </div>
    <div class="kanban-board" id="kanbanBoard">
        @foreach($kanbanCols as $colKey => $col)
        <div class="kanban-col">
            <div class="kanban-col-header" style="background:{{ $col['bg'] }};border-color:{{ $col['color'] }};color:{{ $col['color'] }}">
                <span>{{ $col['label'] }}</span>
                <span style="background:{{ $col['color'] }};color:#fff;border-radius:99px;padding:1px 8px;font-size:0.7rem">{{ $tasksByStatus[$colKey]->count() }}</span>
            </div>
            <div class="kanban-tasks sortable-col" data-status="{{ $colKey }}" id="kol-{{ $colKey }}">
                @foreach($tasksByStatus[$colKey] as $task)
                <div class="kanban-card" data-task-id="{{ $task->id }}"
                     style="border-left-color:{{ $prioColors[$task->priority] ?? '#6B7280' }}">
                    <div class="d-flex align-items-start justify-content-between gap-1 mb-1">
                        <span class="kanban-card-title">{{ $task->title }}</span>
                        <div class="d-flex gap-1 flex-shrink-0">
                            <a href="{{ route('projects.tasks.edit', [$project, $task]) }}" class="btn btn-xs btn-outline-secondary" style="padding:2px 5px"><i class="fas fa-pen" style="font-size:0.6rem"></i></a>
                        </div>
                    </div>
                    <div class="kanban-card-meta">
                        <span class="badge {{ $task->priorityBadgeClass() }}" style="font-size:0.62rem">{{ $task->priorityLabel() }}</span>
                        @if($task->milestone)
                            <span><i class="fas fa-flag me-1"></i>{{ Str::limit($task->milestone->title, 20) }}</span>
                        @endif
                        @if($task->due_date)
                            <span style="{{ $task->isOverdue() ? 'color:#F43F5E' : '' }}">
                                <i class="fas fa-calendar me-1"></i>{{ $task->due_date->format('M d') }}
                            </span>
                        @endif
                        @if($task->estimated_hours)
                            <span><i class="fas fa-clock me-1"></i>{{ number_format($task->actual_hours,1) }}h</span>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            <a href="{{ route('projects.tasks.create', $project) }}?status={{ $colKey }}" class="kanban-add-btn">
                <i class="fas fa-plus me-1"></i>Add task
            </a>
        </div>
        @endforeach
    </div>
</div>

{{-- ─────────────── TAB: FINANCIALS ─────────────── --}}
<div class="tab-content" id="tab-financials" style="display:none">
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="qs-card"><div class="qs-num" style="color:#22C55E">{{ $symbol }}{{ number_format($totalIncome,2) }}</div><div class="qs-lbl">Total Income</div></div>
        </div>
        <div class="col-md-4">
            <div class="qs-card"><div class="qs-num" style="color:#F43F5E">{{ $symbol }}{{ number_format($totalExpenses,2) }}</div><div class="qs-lbl">Total Expenses</div></div>
        </div>
        <div class="col-md-4">
            <div class="qs-card"><div class="qs-num" style="color:{{ $profit >= 0 ? '#22C55E' : '#F43F5E' }}">{{ $symbol }}{{ number_format(abs($profit),2) }}</div><div class="qs-lbl">{{ $profit >= 0 ? 'Profit' : 'Loss' }}</div></div>
        </div>
    </div>

    {{-- Invoices --}}
    <div class="card mb-3">
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h6 class="fw-700 mb-0"><i class="fas fa-file-invoice me-2" style="color:#0E7490"></i>Invoices</h6>
                <a href="{{ route('invoices.create') }}?project_id={{ $project->id }}" class="btn btn-xs btn-outline-secondary">+ New Invoice</a>
            </div>
            @if($invoices->isEmpty())
                <div class="text-center py-3 opacity-55" style="font-size:0.83rem">No invoices linked to this project.</div>
            @else
            <div class="table-responsive">
                <table class="table table-sm mb-0" style="font-size:0.83rem">
                    <thead><tr><th>Invoice #</th><th>Date</th><th>Amount</th><th>Paid</th><th>Status</th><th></th></tr></thead>
                    <tbody>
                        @foreach($invoices as $inv)
                        @php
                            $isBadge = ['draft'=>'secondary','sent'=>'primary','partial'=>'warning','paid'=>'success','overdue'=>'danger','cancelled'=>'secondary'];
                        @endphp
                        <tr>
                            <td class="fw-600">{{ $inv->invoice_number }}</td>
                            <td>{{ $inv->issue_date->format('M d, Y') }}</td>
                            <td>{{ $symbol }}{{ number_format($inv->total_amount,2) }}</td>
                            <td>{{ $symbol }}{{ number_format($inv->paid_amount,2) }}</td>
                            <td><span class="badge bg-{{ $isBadge[$inv->status] ?? 'secondary' }}">{{ ucfirst($inv->status) }}</span></td>
                            <td><a href="{{ route('invoices.show',$inv) }}" class="btn btn-xs btn-outline-secondary"><i class="fas fa-eye"></i></a></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>

    {{-- Income --}}
    <div class="card mb-3">
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h6 class="fw-700 mb-0"><i class="fas fa-arrow-down me-2" style="color:#22C55E"></i>Income</h6>
                <a href="{{ route('income.create') }}?project_id={{ $project->id }}" class="btn btn-xs btn-outline-secondary">+ Add Income</a>
            </div>
            @if($incomes->isEmpty())
                <div class="text-center py-3 opacity-55" style="font-size:0.83rem">No income records linked to this project.</div>
            @else
            <div class="table-responsive">
                <table class="table table-sm mb-0" style="font-size:0.83rem">
                    <thead><tr><th>Date</th><th>Description</th><th>Category</th><th class="text-end">Amount</th></tr></thead>
                    <tbody>
                        @foreach($incomes as $inc)
                        <tr>
                            <td>{{ $inc->income_date->format('M d, Y') }}</td>
                            <td>{{ Str::limit($inc->notes ?? '—',40) }}</td>
                            <td>{{ $inc->category->name ?? '—' }}</td>
                            <td class="text-end fw-600" style="color:#22C55E">{{ $symbol }}{{ number_format($inc->amount,2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot><tr><th colspan="3">Total</th><th class="text-end" style="color:#22C55E">{{ $symbol }}{{ number_format($totalIncome,2) }}</th></tr></tfoot>
                </table>
            </div>
            @endif
        </div>
    </div>

    {{-- Expenses --}}
    <div class="card">
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h6 class="fw-700 mb-0"><i class="fas fa-arrow-up me-2" style="color:#F43F5E"></i>Expenses</h6>
                <a href="{{ route('expenses.create') }}?project_id={{ $project->id }}" class="btn btn-xs btn-outline-secondary">+ Add Expense</a>
            </div>
            @if($expenses->isEmpty())
                <div class="text-center py-3 opacity-55" style="font-size:0.83rem">No expense records linked to this project.</div>
            @else
            <div class="table-responsive">
                <table class="table table-sm mb-0" style="font-size:0.83rem">
                    <thead><tr><th>Date</th><th>Description</th><th>Category</th><th class="text-end">Amount</th></tr></thead>
                    <tbody>
                        @foreach($expenses as $exp)
                        <tr>
                            <td>{{ $exp->expense_date->format('M d, Y') }}</td>
                            <td>{{ Str::limit($exp->notes ?? $exp->vendor_name ?? '—',40) }}</td>
                            <td>{{ $exp->category->name ?? '—' }}</td>
                            <td class="text-end fw-600" style="color:#F43F5E">{{ $symbol }}{{ number_format($exp->amount,2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot><tr><th colspan="3">Total</th><th class="text-end" style="color:#F43F5E">{{ $symbol }}{{ number_format($totalExpenses,2) }}</th></tr></tfoot>
                </table>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- ══════════ MODALS ══════════ --}}

{{-- Milestone Create/Edit Modal --}}
<div class="modal fade" id="milestoneModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="milestoneForm" method="POST" action="{{ route('projects.milestones.store', $project) }}">
                @csrf
                <input type="hidden" name="_method" id="msMeth" value="POST">
                <div class="modal-header">
                    <h6 class="modal-title fw-700" id="msModalTitle"><i class="fas fa-flag me-2"></i>Add Milestone</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-600">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="msTitle" class="form-control" placeholder="e.g. Phase 1: Design" required>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-600">Priority</label>
                            <select name="priority" id="msPriority" class="form-select">
                                <option value="low">Low</option>
                                <option value="medium" selected>Medium</option>
                                <option value="high">High</option>
                                <option value="critical">Critical</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-600">Status</label>
                            <select name="status" id="msStatus" class="form-select">
                                <option value="pending" selected>Pending</option>
                                <option value="in_progress">In Progress</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-600">Start Date</label>
                            <input type="date" name="start_date" id="msStartDate" class="form-control">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-600">Due Date</label>
                            <input type="date" name="due_date" id="msDueDate" class="form-control">
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-600">Milestone Value</label>
                            <div class="input-group">
                                <span class="input-group-text">{{ $symbol }}</span>
                                <input type="number" name="amount" id="msAmount" class="form-control" placeholder="0.00" min="0" step="0.01">
                            </div>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-600">Color</label>
                            <div class="d-flex gap-2 mt-1 flex-wrap" id="msColorPicker">
                                @foreach(['#0E7490','#8B5CF6','#F59E0B','#22C55E','#F43F5E','#3B82F6','#EC4899','#94A3B8'] as $clr)
                                <div class="color-swatch" data-color="{{ $clr }}" onclick="selectColor('{{ $clr }}')"
                                     style="width:24px;height:24px;border-radius:6px;background:{{ $clr }};cursor:pointer;border:2px solid transparent;transition:border 0.15s"></div>
                                @endforeach
                            </div>
                            <input type="hidden" name="color" id="msColor">
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-600">Description</label>
                        <textarea name="description" id="msDesc" class="form-control" rows="2" placeholder="Optional milestone notes…"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-save me-1"></i><span id="msBtnText">Add Milestone</span></button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Delete Milestone Confirm --}}
<div class="modal fade" id="delMsModal" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-4">
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                     style="width:52px;height:52px;background:rgba(244,63,94,0.12)">
                    <i class="fas fa-flag" style="color:#F43F5E"></i>
                </div>
                <h6 class="fw-700 mb-1">Delete Milestone?</h6>
                <p class="text-muted small mb-3" id="delMsName"></p>
                <p class="small opacity-65 mb-3">Tasks linked to this milestone will remain but be unlinked.</p>
                <form id="delMsForm" method="POST">
                    @csrf
                    <input type="hidden" name="_method" value="DELETE">
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-secondary btn-sm flex-fill" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger btn-sm flex-fill">Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Delete Task Confirm --}}
<div class="modal fade" id="delTaskModal" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-4">
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                     style="width:52px;height:52px;background:rgba(244,63,94,0.12)">
                    <i class="fas fa-tasks" style="color:#F43F5E"></i>
                </div>
                <h6 class="fw-700 mb-1">Delete Task?</h6>
                <p class="small opacity-65 mb-3">Time logs for this task will also be deleted.</p>
                <form id="delTaskForm" method="POST">
                    @csrf
                    <input type="hidden" name="_method" value="DELETE">
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-secondary btn-sm flex-fill" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger btn-sm flex-fill">Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Time Log Modal --}}
<div class="modal fade" id="timeLogModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title fw-700"><i class="fas fa-clock me-2"></i>Log Time — <span id="tlTaskName"></span></h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3 mb-3">
                    <div class="col-5">
                        <label class="form-label fw-600">Hours <span class="text-danger">*</span></label>
                        <input type="number" id="tlHours" class="form-control" placeholder="e.g. 2.5" min="0.1" max="24" step="0.25">
                    </div>
                    <div class="col-7">
                        <label class="form-label fw-600">Date <span class="text-danger">*</span></label>
                        <input type="date" id="tlDate" class="form-control" value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-600">Description</label>
                    <input type="text" id="tlDesc" class="form-control" placeholder="What did you work on?">
                </div>
                <button class="btn btn-primary btn-sm w-100" onclick="submitTimeLog()">
                    <i class="fas fa-plus me-1"></i>Log Time
                </button>
                <hr>
                <div id="tlPrevLogs" style="max-height:200px;overflow-y:auto">
                    <div class="text-center opacity-55 py-2" style="font-size:0.8rem">Loading time logs…</div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.3/Sortable.min.js"></script>
<script>
const PROJECT_ID = {{ $project->id }};
const CSRF = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';

// ── Tab switching ────────────────────────────────────────
const tabs   = document.querySelectorAll('.proj-tab');
const panels = document.querySelectorAll('.tab-content');

function switchTab(name) {
    tabs.forEach(t => t.classList.toggle('active', t.dataset.tab === name));
    panels.forEach(p => p.style.display = p.id === 'tab-' + name ? '' : 'none');
    if (name === 'kanban') initKanban();
    history.replaceState(null, '', location.pathname + '?tab=' + name);
}

tabs.forEach(t => t.addEventListener('click', () => switchTab(t.dataset.tab)));

// Activate default tab
const urlTab = new URLSearchParams(location.search).get('tab') || '{{ $activeTab }}';
switchTab(urlTab || 'overview');

// ── Milestone accordion ──────────────────────────────────
function toggleMilestone(id) {
    const el  = document.getElementById('ms-tasks-' + id);
    const chv = document.getElementById('ms-chevron-' + id);
    const open = el.style.display === '';
    el.style.display = open ? 'none' : '';
    chv.style.transform = open ? '' : 'rotate(180deg)';
}

// ── Milestone modal ──────────────────────────────────────
let editMsId = null;
const msForm      = document.getElementById('milestoneForm');
const msMeth      = document.getElementById('msMeth');
const msModalEl   = document.getElementById('milestoneModal');

document.getElementById('milestoneModal').addEventListener('hidden.bs.modal', () => {
    editMsId = null;
    msForm.reset();
    msMeth.value = 'POST';
    msForm.action = '{{ route("projects.milestones.store", $project) }}';
    document.getElementById('msModalTitle').innerHTML = '<i class="fas fa-flag me-2"></i>Add Milestone';
    document.getElementById('msBtnText').textContent  = 'Add Milestone';
    document.querySelectorAll('.color-swatch').forEach(s => s.style.borderColor = 'transparent');
    document.getElementById('msColor').value = '';
});

function editMilestone(id, title, desc, priority, status, startDate, dueDate, amount, color) {
    editMsId = id;
    msMeth.value = 'PUT';
    msForm.action = `/projects/${PROJECT_ID}/milestones/${id}`;
    document.getElementById('msModalTitle').innerHTML = '<i class="fas fa-pen me-2"></i>Edit Milestone';
    document.getElementById('msBtnText').textContent  = 'Save Changes';
    document.getElementById('msTitle').value      = title;
    document.getElementById('msDesc').value       = desc || '';
    document.getElementById('msPriority').value   = priority;
    document.getElementById('msStatus').value     = status;
    document.getElementById('msStartDate').value  = startDate || '';
    document.getElementById('msDueDate').value    = dueDate || '';
    document.getElementById('msAmount').value     = amount > 0 ? amount : '';
    if (color) selectColor(color);
    new bootstrap.Modal(msModalEl).show();
}

function selectColor(c) {
    document.getElementById('msColor').value = c;
    document.querySelectorAll('.color-swatch').forEach(s => {
        s.style.borderColor = s.dataset.color === c ? s.dataset.color : 'transparent';
        s.style.outline     = s.dataset.color === c ? `2px solid ${c}` : 'none';
        s.style.outlineOffset = s.dataset.color === c ? '2px' : '0';
    });
}

// ── Delete milestone ─────────────────────────────────────
function deleteMilestone(id, name) {
    document.getElementById('delMsName').textContent = name;
    document.getElementById('delMsForm').action = `/projects/${PROJECT_ID}/milestones/${id}`;
    new bootstrap.Modal(document.getElementById('delMsModal')).show();
}

// ── Delete task ──────────────────────────────────────────
function deleteTask(projId, taskId) {
    document.getElementById('delTaskForm').action = `/projects/${projId}/tasks/${taskId}`;
    new bootstrap.Modal(document.getElementById('delTaskModal')).show();
}

// ── Quick task toggle (check/uncheck) ────────────────────
async function quickToggleTask(projId, taskId, el) {
    const isDone    = el.classList.contains('done');
    const newStatus = isDone ? 'todo' : 'done';

    const res = await fetch(`/projects/${projId}/tasks/${taskId}/status`, {
        method: 'PATCH',
        headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ status: newStatus })
    });

    if (res.ok) {
        el.classList.toggle('done');
        // Update row style
        const row = el.closest('.task-list-row, .milestone-task-row');
        if (row) {
            row.classList.toggle('done-task', newStatus === 'done');
            const titleEl = row.querySelector('.task-title-text');
            if (titleEl) titleEl.style.textDecoration = newStatus === 'done' ? 'line-through' : '';
        }
    }
}

// ── Task status filter ───────────────────────────────────
document.querySelectorAll('#taskStatusFilter .filter-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('#taskStatusFilter .filter-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        const filter = btn.dataset.filter;
        document.querySelectorAll('#taskListContainer .task-list-row').forEach(row => {
            row.style.display = (filter === 'all' || row.dataset.status === filter) ? '' : 'none';
        });
    });
});

// ── Kanban board with SortableJS ─────────────────────────
let kanbanInit = false;
function initKanban() {
    if (kanbanInit) return;
    kanbanInit = true;
    document.querySelectorAll('.sortable-col').forEach(col => {
        Sortable.create(col, {
            group:     'kanban-tasks',
            animation: 150,
            ghostClass:'sortable-ghost',
            chosenClass:'sortable-chosen',
            onEnd(evt) {
                const taskId   = evt.item.dataset.taskId;
                const newStatus = evt.to.dataset.status;
                const newOrder  = Array.from(evt.to.children).indexOf(evt.item);

                // Update count badges
                updateKanbanCounts();

                fetch(`/projects/${PROJECT_ID}/tasks/${taskId}/status`, {
                    method: 'PATCH',
                    headers: {'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
                    body: JSON.stringify({ status: newStatus })
                });
            }
        });
    });
}

function updateKanbanCounts() {
    document.querySelectorAll('.sortable-col').forEach(col => {
        const header = col.previousElementSibling;
        if (header) {
            const badge = header.querySelector('span:last-child');
            if (badge) badge.textContent = col.querySelectorAll('.kanban-card').length;
        }
    });
}

// ── Time Log modal ───────────────────────────────────────
let currentTaskId = null;
let currentProjId = null;

function openTimeLog(taskId, taskName, projId) {
    currentTaskId = taskId;
    currentProjId = projId;
    document.getElementById('tlTaskName').textContent = taskName;
    document.getElementById('tlHours').value = '';
    document.getElementById('tlDesc').value  = '';
    document.getElementById('tlDate').value  = new Date().toISOString().split('T')[0];
    loadTimeLogs(projId, taskId);
    new bootstrap.Modal(document.getElementById('timeLogModal')).show();
}

async function loadTimeLogs(projId, taskId) {
    // We don't have a dedicated endpoint so just show a placeholder
    document.getElementById('tlPrevLogs').innerHTML = '<div class="text-center opacity-55 py-2" style="font-size:0.8rem">Previous logs will appear after you submit.</div>';
}

async function submitTimeLog() {
    const hours = parseFloat(document.getElementById('tlHours').value);
    const desc  = document.getElementById('tlDesc').value;
    const date  = document.getElementById('tlDate').value;

    if (!hours || hours < 0.1) { alert('Please enter valid hours.'); return; }
    if (!date)                  { alert('Please select a date.');    return; }

    const res = await fetch(`/projects/${currentProjId}/tasks/${currentTaskId}/logs`, {
        method: 'POST',
        headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ hours, description: desc, logged_date: date })
    });

    if (res.ok) {
        const data = await res.json();
        document.getElementById('tlHours').value = '';
        document.getElementById('tlDesc').value  = '';
        document.getElementById('tlPrevLogs').innerHTML =
            `<div class="text-center py-2" style="font-size:0.82rem;color:#22C55E"><i class="fas fa-check-circle me-1"></i>Logged ${hours}h — Total: ${data.total_hours}h</div>`;
    } else {
        alert('Failed to log time. Please try again.');
    }
}
</script>
@endpush
