@extends('layouts.portal')

@section('title', 'Projects')

@section('styles')
<style>
/* ── Project Index ──────────────────────────────────────── */
.project-card {
    border-radius: 12px;
    overflow: hidden;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    cursor: pointer;
    border: 1px solid var(--border-color, rgba(255,255,255,0.08));
    background: var(--card-bg, #fff);
    display: flex;
    flex-direction: column;
    height: 100%;
}
.project-card:hover { transform: translateY(-3px); }
.project-card .status-stripe { height: 4px; width: 100%; }
.project-card .card-body-inner { padding: 1.25rem; display: flex; flex-direction: column; flex: 1; }
.project-avatar {
    width: 40px; height: 40px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-weight: 700; font-size: 1rem; color: #fff; flex-shrink: 0;
    box-shadow: 0 2px 6px rgba(0,0,0,0.18);
}
.project-title { font-weight: 700; font-size: 0.95rem; line-height: 1.3; margin: 0; color: var(--text-h, #0F172A); }
.project-client { font-size: 0.78rem; color: var(--text-muted, #64748B); margin-top: 2px; }
.project-progress-bar { height: 6px; border-radius: 99px; overflow: hidden; margin: 0.75rem 0; }
.project-progress-fill { height: 100%; border-radius: 99px; transition: width 0.4s ease; }
.project-meta-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0.4rem 0.75rem; margin-top: 0.75rem; }
.meta-item { display: flex; align-items: center; gap: 0.35rem; font-size: 0.73rem; color: var(--text-muted, #64748B); }
.meta-item i { width: 14px; flex-shrink: 0; }
.project-footer {
    padding: 0.7rem 1.25rem; border-top: 1px solid var(--border-color, rgba(255,255,255,0.08));
    display: flex; align-items: center; justify-content: space-between;
}
.deadline-badge { font-size: 0.72rem; font-weight: 600; padding: 3px 8px; border-radius: 99px; }
.deadline-badge.overdue { background: rgba(244,63,94,0.15); color: #E11D48; }
.deadline-badge.soon    { background: rgba(245,158,11,0.15); color: #C2800A; }
.deadline-badge.ok      { background: rgba(34,197,94,0.15);  color: #15803D; }
.deadline-badge.none    { background: rgba(148,163,184,0.18); color: #64748B; }
.proj-stat {
    border-radius: 10px; padding: 1rem 1.25rem;
    display: flex; align-items: center; gap: 1rem;
    background: var(--card-bg, #fff); border: 1px solid var(--border-color, rgba(255,255,255,0.08));
}
.proj-stat-icon { width: 44px; height: 44px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; flex-shrink: 0; }
.proj-stat-num { font-size: 1.5rem; font-weight: 800; line-height: 1; color: var(--text-h, #0F172A); }
.proj-stat-lbl { font-size: 0.75rem; color: var(--text-muted, #64748B); margin-top: 2px; }
.proj-empty { text-align: center; padding: 4rem 2rem; border-radius: 16px; border: 2px dashed var(--border-color, rgba(255,255,255,0.1)); background: var(--card-bg, #fff); }
.proj-empty-icon { width: 80px; height: 80px; border-radius: 20px; background: rgba(14,116,144,0.12); display: flex; align-items: center; justify-content: center; font-size: 2rem; color: var(--ft-teal, #0E7490); margin: 0 auto 1.5rem; }
</style>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Projects</h1>
        <p class="page-subtitle">Track milestones, manage tasks and monitor project health</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('projects.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> New Project
        </a>
    </div>
</div>

{{-- Summary Stats --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="proj-stat">
            <div class="proj-stat-icon" style="background:rgba(11,42,74,0.12);color:#0B2A4A"><i class="fas fa-folder"></i></div>
            <div><div class="proj-stat-num">{{ $stats['total'] }}</div><div class="proj-stat-lbl">Total</div></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="proj-stat">
            <div class="proj-stat-icon" style="background:rgba(14,116,144,0.12);color:#0E7490"><i class="fas fa-spinner"></i></div>
            <div><div class="proj-stat-num">{{ $stats['active'] }}</div><div class="proj-stat-lbl">In Progress</div></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="proj-stat">
            <div class="proj-stat-icon" style="background:rgba(34,197,94,0.12);color:#22C55E"><i class="fas fa-check-circle"></i></div>
            <div><div class="proj-stat-num">{{ $stats['completed'] }}</div><div class="proj-stat-lbl">Completed</div></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="proj-stat">
            <div class="proj-stat-icon" style="background:rgba(245,158,11,0.12);color:#F59E0B"><i class="fas fa-pause-circle"></i></div>
            <div><div class="proj-stat-num">{{ $stats['on_hold'] }}</div><div class="proj-stat-lbl">On Hold</div></div>
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="card mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('projects.index') }}" class="row g-2 align-items-center">
            <div class="col-12 col-md-4">
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Search projects…" value="{{ $search }}">
                </div>
            </div>
            <div class="col-6 col-md-3">
                <select name="client_id" class="form-select form-select-sm">
                    <option value="">All Clients</option>
                    @foreach($clients as $client)
                        <option value="{{ $client->id }}" {{ $client_id == $client->id ? 'selected' : '' }}>{{ $client->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-3">
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Statuses</option>
                    <option value="planned"     {{ $status === 'planned'     ? 'selected' : '' }}>Planned</option>
                    <option value="in_progress" {{ $status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="on_hold"     {{ $status === 'on_hold'     ? 'selected' : '' }}>On Hold</option>
                    <option value="completed"   {{ $status === 'completed'   ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled"   {{ $status === 'cancelled'   ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            <div class="col-12 col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm flex-fill">Filter</button>
                @if($search || $status || $client_id)
                    <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary btn-sm">Clear</a>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- Project Grid --}}
@if($projects->isEmpty())
    <div class="proj-empty">
        <div class="proj-empty-icon"><i class="fas fa-folder-open"></i></div>
        @if($search || $status || $client_id)
            <h5 class="fw-bold mb-2">No projects match your filters</h5>
            <p class="opacity-65 mb-3">Try adjusting your search or filter criteria.</p>
            <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary btn-sm">Clear filters</a>
        @else
            <h5 class="fw-bold mb-2">No projects yet</h5>
            <p class="opacity-65 mb-3">Create your first project to start tracking milestones, tasks and budgets.</p>
            <a href="{{ route('projects.create') }}" class="btn btn-primary"><i class="fas fa-plus me-1"></i> Create First Project</a>
        @endif
    </div>
@else
    <div class="row g-3">
        @foreach($projects as $project)
            @php
                $statusColors = [
                    'planned'=>'#94A3B8','in_progress'=>'#0E7490',
                    'on_hold'=>'#F59E0B','completed'=>'#22C55E','cancelled'=>'#F43F5E',
                ];
                $sc = $statusColors[$project->status] ?? '#94A3B8';
                $statusLabels = [
                    'planned'=>['label'=>'Planned','bg'=>'rgba(148,163,184,0.15)','color'=>'#94A3B8'],
                    'in_progress'=>['label'=>'In Progress','bg'=>'rgba(14,116,144,0.15)','color'=>'#0E7490'],
                    'on_hold'=>['label'=>'On Hold','bg'=>'rgba(245,158,11,0.15)','color'=>'#F59E0B'],
                    'completed'=>['label'=>'Completed','bg'=>'rgba(34,197,94,0.15)','color'=>'#22C55E'],
                    'cancelled'=>['label'=>'Cancelled','bg'=>'rgba(244,63,94,0.15)','color'=>'#F43F5E'],
                ];
                $sl = $statusLabels[$project->status] ?? $statusLabels['planned'];
                $tasksDone  = $project->completed_tasks_count ?? 0;
                $tasksTotal = $project->tasks_count ?? 0;
                $msDone  = $project->completed_milestones_count ?? 0;
                $msTotal = $project->milestones_count ?? 0;
                $days = $project->daysRemaining();
                if ($project->deadline === null) { $dlLabel = 'No deadline'; $dlClass = 'none'; }
                elseif ($days < 0) { $dlLabel = abs($days).'d overdue'; $dlClass = 'overdue'; }
                elseif ($days <= 7) { $dlLabel = $days.'d left'; $dlClass = 'soon'; }
                else { $dlLabel = $project->deadline->format('M d'); $dlClass = 'ok'; }
            @endphp
            <div class="col-12 col-sm-6 col-xl-4">
                <div class="project-card h-100" onclick="window.location='{{ route('projects.show', $project) }}'">
                    <div class="status-stripe" style="background:{{ $sc }}"></div>
                    <div class="card-body-inner">
                        <div class="d-flex align-items-start gap-3 mb-3">
                            <div class="project-avatar" style="background:{{ $sc }}">{{ strtoupper(substr($project->title,0,1)) }}</div>
                            <div class="flex-fill" style="min-width:0">
                                <div class="project-title text-truncate">{{ $project->title }}</div>
                                <div class="project-client"><i class="fas fa-user me-1" style="font-size:0.65rem"></i>{{ $project->client->name ?? '—' }}</div>
                            </div>
                            <span class="badge" style="background:{{ $sl['bg'] }};color:{{ $sl['color'] }};font-size:0.68rem;white-space:nowrap">{{ $sl['label'] }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center" style="font-size:0.75rem">
                            <span class="opacity-65">Progress</span>
                            <span class="fw-bold">{{ $project->progress_percent }}%</span>
                        </div>
                        <div class="project-progress-bar">
                            <div class="project-progress-fill" style="width:{{ $project->progress_percent }}%;background:{{ $sc }}"></div>
                        </div>
                        <div class="project-meta-grid">
                            <div class="meta-item"><i class="fas fa-tasks" style="color:#0E7490"></i><span>{{ $tasksDone }}/{{ $tasksTotal }} tasks</span></div>
                            <div class="meta-item"><i class="fas fa-flag" style="color:#8B5CF6"></i><span>{{ $msDone }}/{{ $msTotal }} milestones</span></div>
                            @if($project->budget > 0)
                            <div class="meta-item"><i class="fas fa-wallet" style="color:#F59E0B"></i><span>Budget: {{ number_format($project->budget,0) }}</span></div>
                            @endif
                            @if($project->start_date)
                            <div class="meta-item"><i class="fas fa-play" style="color:#22D3EE"></i><span>{{ $project->start_date->format('M d, Y') }}</span></div>
                            @endif
                        </div>
                    </div>
                    <div class="project-footer">
                        <span class="deadline-badge {{ $dlClass }}"><i class="fas fa-clock me-1"></i>{{ $dlLabel }}</span>
                        <div class="d-flex gap-1" onclick="event.stopPropagation()">
                            <a href="{{ route('projects.show', $project) }}" class="btn btn-xs btn-outline-secondary" title="View"><i class="fas fa-eye"></i></a>
                            <a href="{{ route('projects.edit', $project) }}" class="btn btn-xs btn-outline-secondary" title="Edit"><i class="fas fa-pen"></i></a>
                            <button type="button" class="btn btn-xs btn-outline-danger"
                                onclick="confirmDelete({{ $project->id }}, '{{ addslashes($project->title) }}')" title="Delete"><i class="fas fa-trash"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @if($projects->hasPages())
        <div class="mt-4">{{ $projects->appends(request()->except('page'))->links() }}</div>
    @endif
@endif

{{-- Delete Modal --}}
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-4">
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                     style="width:56px;height:56px;background:rgba(244,63,94,0.12)">
                    <i class="fas fa-trash" style="color:#F43F5E;font-size:1.25rem"></i>
                </div>
                <h6 class="fw-bold mb-1">Delete Project?</h6>
                <p class="text-muted small mb-0" id="delProjName"></p>
                <p class="small opacity-65 mt-2 mb-3">All tasks, milestones and time logs will also be deleted.</p>
                <form id="deleteForm" method="POST">
                    @csrf @method('DELETE')
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-secondary btn-sm flex-fill" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger btn-sm flex-fill">Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmDelete(id, name) {
    document.getElementById('delProjName').textContent = name;
    document.getElementById('deleteForm').action = '/projects/' + id;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>
@endpush
