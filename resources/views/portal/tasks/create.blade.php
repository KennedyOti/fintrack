@extends('layouts.portal')

@section('title', 'New Task — '.$project->title)

@section('styles')
<style>
.form-card { border-radius: 14px; border: 1px solid var(--border-color, rgba(255,255,255,0.08)); background: var(--card-bg); }
.picker-btn {
    padding: 0.45rem 1rem; border-radius: 8px; border: 1px solid var(--border-color, rgba(255,255,255,0.12));
    cursor: pointer; font-size: 0.82rem; font-weight: 600; background: transparent; color: inherit; transition: all 0.15s;
}
.picker-btn.active { color: #fff; border-color: transparent; }
</style>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">New Task</h1>
        <p class="page-subtitle">
            <a href="{{ route('projects.show', $project) }}" class="text-decoration-none opacity-65">{{ $project->title }}</a>
        </p>
    </div>
    <div class="page-actions">
        <a href="{{ route('projects.show', $project) }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i>Back
        </a>
    </div>
</div>

@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
    <strong>Please fix the errors below:</strong>
    <ul class="mb-0 mt-1 ps-3">
        @foreach($errors->all() as $error)<li style="font-size:0.85rem">{{ $error }}</li>@endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<form method="POST" action="{{ route('projects.tasks.store', $project) }}">
    @csrf
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="form-card p-4">
                <h6 class="fw-700 mb-4"><i class="fas fa-plus me-2 opacity-50"></i>Task Details</h6>

                <div class="mb-3">
                    <label class="form-label fw-600">Task Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                           value="{{ old('title') }}" placeholder="e.g. Create wireframes, Write unit tests…" required autofocus>
                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-600">Milestone</label>
                    <select name="milestone_id" class="form-select @error('milestone_id') is-invalid @enderror">
                        <option value="">No milestone (standalone task)</option>
                        @foreach($milestones as $ms)
                            <option value="{{ $ms->id }}"
                                {{ old('milestone_id', $selectedMilestone) == $ms->id ? 'selected' : '' }}>
                                {{ $ms->title }}
                            </option>
                        @endforeach
                    </select>
                    @error('milestone_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    @if($milestones->isEmpty())
                        <div class="form-text">
                            <a href="#" onclick="history.back()">Go back</a> and add milestones to organize tasks by phase.
                        </div>
                    @endif
                </div>

                <div class="mb-3">
                    <label class="form-label fw-600">Priority <span class="text-danger">*</span></label>
                    <input type="hidden" name="priority" id="priorityInput" value="{{ old('priority', 'medium') }}">
                    <div class="d-flex gap-2 flex-wrap">
                        @foreach([
                            ['low',      'Low',      '#6B7280'],
                            ['medium',   'Medium',   '#0E7490'],
                            ['high',     'High',     '#F59E0B'],
                            ['critical', 'Critical', '#F43F5E'],
                        ] as [$val, $lbl, $clr])
                        <button type="button" class="picker-btn {{ old('priority','medium') === $val ? 'active' : '' }}"
                                data-value="{{ $val }}" data-color="{{ $clr }}"
                                style="{{ old('priority','medium') === $val ? 'background:'.$clr.';border-color:'.$clr.';color:#fff' : 'border-color:'.$clr.';color:'.$clr }}"
                                onclick="selectPicker('priority', '{{ $val }}', '{{ $clr }}')">
                            <i class="fas fa-circle me-1" style="font-size:0.55rem"></i>{{ $lbl }}
                        </button>
                        @endforeach
                    </div>
                    @error('priority')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-600">Status <span class="text-danger">*</span></label>
                    <input type="hidden" name="status" id="statusInput" value="{{ old('status', request('status', 'todo')) }}">
                    <div class="d-flex gap-2 flex-wrap">
                        @foreach([
                            ['todo',        'To Do',       '#94A3B8'],
                            ['in_progress', 'In Progress', '#3B82F6'],
                            ['in_review',   'In Review',   '#8B5CF6'],
                            ['blocked',     'Blocked',     '#F43F5E'],
                            ['done',        'Done',        '#22C55E'],
                        ] as [$val, $lbl, $clr])
                        @php $selSt = old('status', request('status', 'todo')); @endphp
                        <button type="button" class="picker-btn {{ $selSt === $val ? 'active' : '' }}"
                                data-value="{{ $val }}" data-group="status" data-color="{{ $clr }}"
                                style="{{ $selSt === $val ? 'background:'.$clr.';border-color:'.$clr.';color:#fff' : '' }}"
                                onclick="selectPicker('status', '{{ $val }}', '{{ $clr }}')">{{ $lbl }}</button>
                        @endforeach
                    </div>
                    @error('status')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-600">Start Date</label>
                        <input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror"
                               value="{{ old('start_date') }}">
                        @error('start_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-600">Due Date</label>
                        <input type="date" name="due_date" class="form-control @error('due_date') is-invalid @enderror"
                               value="{{ old('due_date') }}">
                        @error('due_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-600">Estimated Hours</label>
                    <div class="input-group" style="max-width:200px">
                        <input type="number" name="estimated_hours" class="form-control @error('estimated_hours') is-invalid @enderror"
                               value="{{ old('estimated_hours') }}" placeholder="e.g. 8" min="0" step="0.5">
                        <span class="input-group-text">h</span>
                    </div>
                    @error('estimated_hours')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <div class="form-text">Helps calculate time tracking accuracy and project estimates.</div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-600">Description / Notes</label>
                    <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                              rows="3" placeholder="Acceptance criteria, notes, or any relevant details…">{{ old('description') }}</textarea>
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-plus me-1"></i>Create Task</button>
                    <a href="{{ route('projects.show', $project) }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">
            <div class="form-card p-4 mb-3">
                <h6 class="fw-700 mb-3"><i class="fas fa-folder me-2" style="color:#0E7490"></i>Project</h6>
                <div class="d-flex align-items-center gap-3">
                    <div style="width:36px;height:36px;border-radius:9px;background:{{ $project->statusColor() }};display:flex;align-items:center;justify-content:center;font-size:1rem;font-weight:800;color:#fff">
                        {{ strtoupper(substr($project->title,0,1)) }}
                    </div>
                    <div>
                        <div class="fw-700" style="font-size:0.88rem">{{ $project->title }}</div>
                        <div style="font-size:0.75rem;opacity:0.6">{{ $project->client->name ?? '—' }}</div>
                    </div>
                </div>
                @if($project->deadline)
                <div class="mt-3 pt-3 border-top" style="font-size:0.8rem;opacity:0.7">
                    <i class="fas fa-flag-checkered me-1"></i>Project deadline: {{ $project->deadline->format('M d, Y') }}
                </div>
                @endif
            </div>

            <div class="form-card p-4">
                <h6 class="fw-700 mb-3"><i class="fas fa-lightbulb me-2" style="color:#F59E0B"></i>Tips</h6>
                <ul class="list-unstyled mb-0" style="font-size:0.8rem;line-height:1.9;opacity:0.8">
                    <li><i class="fas fa-arrow-right me-2" style="color:#0E7490"></i>Set a due date to get deadline alerts</li>
                    <li><i class="fas fa-arrow-right me-2" style="color:#0E7490"></i>Estimate hours for accurate reporting</li>
                    <li><i class="fas fa-arrow-right me-2" style="color:#0E7490"></i>Link to a milestone to group tasks</li>
                    <li><i class="fas fa-arrow-right me-2" style="color:#0E7490"></i>Use "Critical" for blockers</li>
                    <li><i class="fas fa-arrow-right me-2" style="color:#0E7490"></i>Log time after creating the task</li>
                </ul>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
function selectPicker(group, val, color) {
    document.getElementById(group + 'Input').value = val;
    document.querySelectorAll(`.picker-btn[onclick*="'${group}'"]`).forEach(btn => {
        const isActive = btn.dataset.value === val;
        btn.classList.toggle('active', isActive);
        if (group === 'priority') {
            btn.style.background  = isActive ? btn.dataset.color : '';
            btn.style.borderColor = btn.dataset.color;
            btn.style.color       = isActive ? '#fff' : btn.dataset.color;
        } else {
            btn.style.background  = isActive ? btn.dataset.color : '';
            btn.style.borderColor = isActive ? btn.dataset.color : '';
            btn.style.color       = isActive ? '#fff' : '';
        }
    });
}
</script>
@endpush
