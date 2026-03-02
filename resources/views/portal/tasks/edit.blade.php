@extends('layouts.portal')

@section('title', 'Edit Task — '.$project->title)

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
        <h1 class="page-title">Edit Task</h1>
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

<form method="POST" action="{{ route('projects.tasks.update', [$project, $task]) }}">
    @csrf @method('PUT')
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="form-card p-4">
                <h6 class="fw-700 mb-4"><i class="fas fa-pen me-2 opacity-50"></i>Task Details</h6>

                <div class="mb-3">
                    <label class="form-label fw-600">Task Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                           value="{{ old('title', $task->title) }}" required autofocus>
                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-600">Milestone</label>
                    <select name="milestone_id" class="form-select @error('milestone_id') is-invalid @enderror">
                        <option value="">No milestone (standalone task)</option>
                        @foreach($milestones as $ms)
                            <option value="{{ $ms->id }}" {{ old('milestone_id', $task->milestone_id) == $ms->id ? 'selected' : '' }}>
                                {{ $ms->title }}
                            </option>
                        @endforeach
                    </select>
                    @error('milestone_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-600">Priority <span class="text-danger">*</span></label>
                    @php $curPrio = old('priority', $task->priority); @endphp
                    <input type="hidden" name="priority" id="priorityInput" value="{{ $curPrio }}">
                    <div class="d-flex gap-2 flex-wrap">
                        @foreach([
                            ['low',      'Low',      '#6B7280'],
                            ['medium',   'Medium',   '#0E7490'],
                            ['high',     'High',     '#F59E0B'],
                            ['critical', 'Critical', '#F43F5E'],
                        ] as [$val, $lbl, $clr])
                        <button type="button" class="picker-btn {{ $curPrio === $val ? 'active' : '' }}"
                                data-value="{{ $val }}" data-color="{{ $clr }}"
                                style="{{ $curPrio === $val ? 'background:'.$clr.';border-color:'.$clr.';color:#fff' : 'border-color:'.$clr.';color:'.$clr }}"
                                onclick="selectPicker('priority', '{{ $val }}', '{{ $clr }}')">
                            <i class="fas fa-circle me-1" style="font-size:0.55rem"></i>{{ $lbl }}
                        </button>
                        @endforeach
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-600">Status <span class="text-danger">*</span></label>
                    @php $curSt = old('status', $task->status); @endphp
                    <input type="hidden" name="status" id="statusInput" value="{{ $curSt }}">
                    <div class="d-flex gap-2 flex-wrap">
                        @foreach([
                            ['todo',        'To Do',       '#94A3B8'],
                            ['in_progress', 'In Progress', '#3B82F6'],
                            ['in_review',   'In Review',   '#8B5CF6'],
                            ['blocked',     'Blocked',     '#F43F5E'],
                            ['done',        'Done',        '#22C55E'],
                        ] as [$val, $lbl, $clr])
                        <button type="button" class="picker-btn {{ $curSt === $val ? 'active' : '' }}"
                                data-value="{{ $val }}" data-group="status" data-color="{{ $clr }}"
                                style="{{ $curSt === $val ? 'background:'.$clr.';border-color:'.$clr.';color:#fff' : '' }}"
                                onclick="selectPicker('status', '{{ $val }}', '{{ $clr }}')">{{ $lbl }}</button>
                        @endforeach
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-600">Start Date</label>
                        <input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror"
                               value="{{ old('start_date', $task->start_date?->format('Y-m-d')) }}">
                        @error('start_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-600">Due Date</label>
                        <input type="date" name="due_date" class="form-control @error('due_date') is-invalid @enderror"
                               value="{{ old('due_date', $task->due_date?->format('Y-m-d')) }}">
                        @error('due_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-600">Estimated Hours</label>
                    <div class="input-group" style="max-width:200px">
                        <input type="number" name="estimated_hours" class="form-control @error('estimated_hours') is-invalid @enderror"
                               value="{{ old('estimated_hours', $task->estimated_hours > 0 ? $task->estimated_hours : '') }}"
                               placeholder="e.g. 8" min="0" step="0.5">
                        <span class="input-group-text">h</span>
                    </div>
                    @if($task->actual_hours > 0)
                    <div class="form-text">{{ number_format($task->actual_hours, 2) }}h already logged on this task.</div>
                    @endif
                    @error('estimated_hours')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-4">
                    <label class="form-label fw-600">Description / Notes</label>
                    <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                              rows="3">{{ old('description', $task->description) }}</textarea>
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Save Changes</button>
                    <a href="{{ route('projects.show', $project) }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">
            <div class="form-card p-4 mb-3">
                <h6 class="fw-700 mb-3"><i class="fas fa-info-circle me-2 opacity-50"></i>Task Info</h6>
                <dl class="mb-0" style="font-size:0.83rem">
                    <div class="d-flex justify-content-between mb-2">
                        <dt class="fw-normal opacity-65">Created</dt>
                        <dd class="mb-0 fw-600">{{ $task->created_at->format('M d, Y') }}</dd>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <dt class="fw-normal opacity-65">Hours Logged</dt>
                        <dd class="mb-0 fw-600">{{ number_format($task->actual_hours, 2) }}h</dd>
                    </div>
                    @if($task->completed_at)
                    <div class="d-flex justify-content-between mb-2">
                        <dt class="fw-normal opacity-65">Completed</dt>
                        <dd class="mb-0 fw-600">{{ $task->completed_at->format('M d, Y') }}</dd>
                    </div>
                    @endif
                </dl>
            </div>

            <div class="form-card p-4" style="border-color:rgba(244,63,94,0.25)">
                <h6 class="fw-700 mb-2" style="color:#F43F5E"><i class="fas fa-exclamation-triangle me-2"></i>Danger Zone</h6>
                <p class="small opacity-65 mb-3">Deleting this task will also remove all time logs.</p>
                <form method="POST" action="{{ route('projects.tasks.destroy', [$project, $task]) }}" onsubmit="return confirm('Delete this task?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                        <i class="fas fa-trash me-1"></i>Delete Task
                    </button>
                </form>
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
