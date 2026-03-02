@extends('layouts.portal')

@section('title', 'New Project')

@section('styles')
<style>
.form-card { border-radius: 14px; border: 1px solid var(--border-color, rgba(255,255,255,0.08)); background: var(--card-bg); }
.status-btn { padding: 0.5rem 1rem; border-radius: 8px; border: 1px solid var(--border-color, rgba(255,255,255,0.12)); cursor: pointer; font-size: 0.82rem; font-weight: 600; transition: all 0.15s; background: transparent; color: inherit; }
.status-btn.active { color: #fff; border-color: transparent; }
.progress-slider { accent-color: var(--ft-teal, #0E7490); }
</style>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">New Project</h1>
        <p class="page-subtitle">Set up a new project to track milestones, tasks and financials</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i>Back
        </a>
    </div>
</div>

<form method="POST" action="{{ route('projects.store') }}" id="projectForm">
    @csrf
    <div class="row g-4">
        {{-- Main Form --}}
        <div class="col-lg-8">
            <div class="form-card p-4">
                <h6 class="fw-700 mb-4"><i class="fas fa-folder-plus me-2 opacity-50"></i>Project Details</h6>

                <div class="mb-3">
                    <label class="form-label fw-600">Project Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                           value="{{ old('title') }}" placeholder="e.g. Brand Identity Design, E-Commerce Platform…" required>
                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-600">Client <span class="text-danger">*</span></label>
                    <select name="client_id" class="form-select @error('client_id') is-invalid @enderror" required>
                        <option value="">Select client…</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                {{ $client->name }}{{ $client->company_name ? ' — '.$client->company_name : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('client_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    @if($clients->isEmpty())
                        <div class="form-text text-warning"><i class="fas fa-exclamation-triangle me-1"></i>
                            No active clients. <a href="{{ route('clients.create') }}">Create a client first</a>.
                        </div>
                    @endif
                </div>

                <div class="mb-3">
                    <label class="form-label fw-600">Description</label>
                    <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                              rows="3" placeholder="Describe the project scope, deliverables, or key objectives…">{{ old('description') }}</textarea>
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-600">Start Date <span class="text-danger">*</span></label>
                        <input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror"
                               value="{{ old('start_date', date('Y-m-d')) }}" required>
                        @error('start_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-600">Deadline</label>
                        <input type="date" name="deadline" class="form-control @error('deadline') is-invalid @enderror"
                               value="{{ old('deadline') }}">
                        @error('deadline')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-600">Budget</label>
                    <div class="input-group">
                        <span class="input-group-text">{{ \App\Helpers\CurrencyHelper::getSymbol(auth()->user()->currency_code ?? 'USD') }}</span>
                        <input type="number" name="budget" class="form-control @error('budget') is-invalid @enderror"
                               value="{{ old('budget') }}" placeholder="0.00" min="0" step="0.01">
                    </div>
                    @error('budget')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <div class="form-text">Optional — helps track budget utilisation as expenses are added.</div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-600 d-flex justify-content-between">
                        <span>Status</span>
                    </label>
                    <input type="hidden" name="status" id="statusInput" value="{{ old('status', 'planned') }}">
                    <div class="d-flex flex-wrap gap-2" id="statusBtns">
                        @foreach([
                            ['planned',     'Planned',     '#94A3B8'],
                            ['in_progress', 'In Progress', '#0E7490'],
                            ['on_hold',     'On Hold',     '#F59E0B'],
                            ['completed',   'Completed',   '#22C55E'],
                            ['cancelled',   'Cancelled',   '#F43F5E'],
                        ] as [$val, $lbl, $clr])
                        <button type="button" class="status-btn {{ old('status','planned') === $val ? 'active' : '' }}"
                                data-value="{{ $val }}" data-color="{{ $clr }}"
                                style="{{ old('status','planned') === $val ? 'background:'.$clr.';border-color:'.$clr : '' }}"
                                onclick="selectStatus('{{ $val }}','{{ $clr }}')">{{ $lbl }}</button>
                        @endforeach
                    </div>
                    @error('status')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>

                <div class="mb-1">
                    <label class="form-label fw-600 d-flex justify-content-between">
                        <span>Initial Progress</span>
                        <span id="progressDisplay" class="fw-700" style="color:var(--ft-teal,#0E7490)">{{ old('progress_percent', 0) }}%</span>
                    </label>
                    <input type="range" name="progress_percent" id="progressSlider" class="form-range progress-slider"
                           min="0" max="100" value="{{ old('progress_percent', 0) }}"
                           oninput="document.getElementById('progressDisplay').textContent=this.value+'%'">
                    <input type="hidden" name="progress_percent" id="progressVal" value="{{ old('progress_percent', 0) }}">
                </div>
                <div class="form-text mb-3">Typically 0% for new projects. You can update this as work progresses.</div>

                <div class="d-flex gap-2 pt-2">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-plus me-1"></i>Create Project</button>
                    <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </div>
        </div>

        {{-- Sidebar Tips --}}
        <div class="col-lg-4">
            <div class="form-card p-4 mb-3">
                <h6 class="fw-700 mb-3"><i class="fas fa-lightbulb me-2" style="color:#F59E0B"></i>Getting Started</h6>
                <ul class="list-unstyled mb-0" style="font-size:0.82rem;line-height:1.8">
                    <li class="mb-2"><i class="fas fa-check-circle me-2" style="color:#22C55E"></i>Create the project first</li>
                    <li class="mb-2"><i class="fas fa-flag me-2" style="color:#8B5CF6"></i>Then add milestones (phases)</li>
                    <li class="mb-2"><i class="fas fa-tasks me-2" style="color:#0E7490"></i>Break milestones into tasks</li>
                    <li class="mb-2"><i class="fas fa-clock me-2" style="color:#3B82F6"></i>Log time on tasks as you work</li>
                    <li><i class="fas fa-file-invoice me-2" style="color:#F59E0B"></i>Link invoices & expenses automatically</li>
                </ul>
            </div>
            <div class="form-card p-4">
                <h6 class="fw-700 mb-3"><i class="fas fa-users me-2 opacity-50"></i>Great for…</h6>
                <div class="d-flex flex-wrap gap-2">
                    @foreach(['Web Design','Brand Identity','App Development','Content Strategy','Video Production','Photography','Consulting','Architecture','Marketing Campaign'] as $tag)
                    <span class="badge" style="background:rgba(14,116,144,0.1);color:#0E7490;font-weight:500;font-size:0.75rem">{{ $tag }}</span>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
function selectStatus(val, color) {
    document.getElementById('statusInput').value = val;
    document.querySelectorAll('.status-btn').forEach(btn => {
        const isActive = btn.dataset.value === val;
        btn.classList.toggle('active', isActive);
        btn.style.background    = isActive ? btn.dataset.color : '';
        btn.style.borderColor   = isActive ? btn.dataset.color : '';
        btn.style.color         = isActive ? '#fff' : '';
    });
}

// Sync range to hidden input
document.getElementById('progressSlider').addEventListener('input', function() {
    document.getElementById('progressVal').value = this.value;
});
// Remove the range input name so only hidden sends
document.getElementById('progressSlider').removeAttribute('name');
</script>
@endpush
