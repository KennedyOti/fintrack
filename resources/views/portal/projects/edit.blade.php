@extends('layouts.portal')

@section('title', 'Edit — '.$project->title)

@section('styles')
<style>
.form-card {
    border-radius: 14px;
    border: 1px solid var(--border-color, rgba(255,255,255,0.08));
    background: var(--card-bg, #fff);
}
.status-btn {
    padding: 0.5rem 1rem; border-radius: 8px;
    border: 1.5px solid var(--border-color, rgba(255,255,255,0.12));
    cursor: pointer; font-size: 0.82rem; font-weight: 600;
    transition: all 0.15s; background: var(--card-bg, transparent);
    color: var(--text-body, inherit);
    display: inline-flex; align-items: center; gap: 5px;
}
.status-btn.active { color: #fff; border-color: transparent; }
.progress-slider { accent-color: var(--ft-teal, #0E7490); }
</style>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Edit Project</h1>
        <p class="page-subtitle">{{ $project->title }}</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('projects.show', $project) }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i>Back to Project
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

<form method="POST" action="{{ route('projects.update', $project) }}" id="projectForm">
    @csrf @method('PUT')
    <div class="row g-4">
        {{-- Main Form --}}
        <div class="col-lg-8">
            <div class="form-card p-4">
                <h6 class="fw-700 mb-4"><i class="fas fa-pen me-2 opacity-50"></i>Project Details</h6>

                <div class="mb-3">
                    <label class="form-label fw-600">Project Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                           value="{{ old('title', $project->title) }}" required>
                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-600">Client <span class="text-danger">*</span></label>
                    <select name="client_id" class="form-select @error('client_id') is-invalid @enderror" required>
                        <option value="">Select client…</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}" {{ old('client_id', $project->client_id) == $client->id ? 'selected' : '' }}>
                                {{ $client->name }}{{ $client->company_name ? ' — '.$client->company_name : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('client_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-600">Description</label>
                    <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                              rows="3">{{ old('description', $project->description) }}</textarea>
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-600">Start Date <span class="text-danger">*</span></label>
                        <input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror"
                               value="{{ old('start_date', $project->start_date?->format('Y-m-d')) }}" required>
                        @error('start_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-600">Deadline</label>
                        <input type="date" name="deadline" class="form-control @error('deadline') is-invalid @enderror"
                               value="{{ old('deadline', $project->deadline?->format('Y-m-d')) }}">
                        @error('deadline')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-600">Budget</label>
                    <div class="input-group">
                        <span class="input-group-text">{{ \App\Helpers\CurrencyHelper::getSymbol(auth()->user()->currency_code ?? 'USD') }}</span>
                        <input type="number" name="budget" class="form-control @error('budget') is-invalid @enderror"
                               value="{{ old('budget', $project->budget > 0 ? $project->budget : '') }}"
                               placeholder="0.00" min="0" step="0.01">
                    </div>
                    @error('budget')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-600">Status</label>
                    @php $currentStatus = old('status', $project->status); @endphp
                    <input type="hidden" name="status" id="statusInput" value="{{ $currentStatus }}">
                    <div class="d-flex flex-wrap gap-2" id="statusBtns">
                        @foreach([
                            ['planned',     'Planned',     '#94A3B8'],
                            ['in_progress', 'In Progress', '#0E7490'],
                            ['on_hold',     'On Hold',     '#F59E0B'],
                            ['completed',   'Completed',   '#22C55E'],
                            ['cancelled',   'Cancelled',   '#F43F5E'],
                        ] as [$val, $lbl, $clr])
                        <button type="button" class="status-btn {{ $currentStatus === $val ? 'active' : '' }}"
                                data-value="{{ $val }}" data-color="{{ $clr }}"
                                style="{{ $currentStatus === $val ? 'background:'.$clr.';border-color:'.$clr.';color:#fff' : '' }}"
                                onclick="selectStatus('{{ $val }}','{{ $clr }}')">{{ $lbl }}</button>
                        @endforeach
                    </div>
                    @error('status')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>

                <div class="mb-1">
                    @php $prog = old('progress_percent', $project->progress_percent); @endphp
                    <label class="form-label fw-600 d-flex justify-content-between">
                        <span>Progress</span>
                        <span id="progressDisplay" class="fw-700" style="color:var(--ft-teal,#0E7490)">{{ $prog }}%</span>
                    </label>
                    <input type="range" id="progressSlider" class="form-range progress-slider"
                           min="0" max="100" value="{{ $prog }}"
                           oninput="document.getElementById('progressDisplay').textContent=this.value+'%';document.getElementById('progressVal').value=this.value">
                    <input type="hidden" name="progress_percent" id="progressVal" value="{{ $prog }}">
                </div>

                <div class="d-flex gap-2 pt-3">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Save Changes</button>
                    <a href="{{ route('projects.show', $project) }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">
            <div class="form-card p-4 mb-3">
                <h6 class="fw-700 mb-3"><i class="fas fa-chart-pie me-2 opacity-50"></i>Project Stats</h6>
                <dl class="mb-0" style="font-size:0.83rem">
                    <div class="d-flex justify-content-between mb-2">
                        <dt class="fw-normal opacity-65">Created</dt>
                        <dd class="mb-0 fw-600">{{ $project->created_at->format('M d, Y') }}</dd>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <dt class="fw-normal opacity-65">Tasks</dt>
                        <dd class="mb-0 fw-600">{{ $project->tasks()->count() }}</dd>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <dt class="fw-normal opacity-65">Milestones</dt>
                        <dd class="mb-0 fw-600">{{ $project->milestones()->count() }}</dd>
                    </div>
                    <div class="d-flex justify-content-between">
                        <dt class="fw-normal opacity-65">Hours Logged</dt>
                        <dd class="mb-0 fw-600">{{ number_format($project->totalHoursLogged(), 1) }}h</dd>
                    </div>
                </dl>
            </div>

            {{-- Danger Zone --}}
            <div class="form-card p-4" style="border-color:rgba(244,63,94,0.25)">
                <h6 class="fw-700 mb-2" style="color:#F43F5E"><i class="fas fa-exclamation-triangle me-2"></i>Danger Zone</h6>
                <p class="small opacity-65 mb-3">Deleting this project will permanently remove all tasks, milestones and time logs.</p>
                <button type="button" class="btn btn-outline-danger btn-sm w-100" data-bs-toggle="modal" data-bs-target="#deleteModal">
                    <i class="fas fa-trash me-1"></i>Delete Project
                </button>
            </div>
        </div>
    </div>
</form>

{{-- Delete Confirm Modal --}}
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-4">
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                     style="width:52px;height:52px;background:rgba(244,63,94,0.12)">
                    <i class="fas fa-trash" style="color:#F43F5E"></i>
                </div>
                <h6 class="fw-700 mb-1">Delete "{{ $project->title }}"?</h6>
                <p class="small opacity-65 mb-3">This cannot be undone. All tasks, milestones and time logs will be deleted.</p>
                <form method="POST" action="{{ route('projects.destroy', $project) }}">
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
function selectStatus(val, color) {
    document.getElementById('statusInput').value = val;
    document.querySelectorAll('.status-btn').forEach(btn => {
        const isActive = btn.dataset.value === val;
        btn.classList.toggle('active', isActive);
        btn.style.background  = isActive ? btn.dataset.color : '';
        btn.style.borderColor = isActive ? btn.dataset.color : '';
        btn.style.color       = isActive ? '#fff' : '';
    });
}
</script>
@endpush
