@extends('layouts.portal')

@section('title', $recurring->name . ' - FinTrack')

@section('content')
<!-- Page Header -->
<div class="page-header mb-4">
    <div>
        <h4 class="page-title">{{ $recurring->name }}</h4>
        <p class="page-subtitle">Recurring {{ ucfirst($recurring->type) }}</p>
    </div>
    <div class="page-actions d-flex gap-2">
        <a href="{{ route('recurring.edit', $recurring) }}" class="btn btn-outline-secondary">
            <i class="fas fa-edit me-1"></i> Edit
        </a>
        <a href="{{ route('recurring.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
    </div>
</div>

@if($recurring->isOverdue())
<div class="alert alert-warning border-0 shadow-sm mb-4">
    <i class="fas fa-exclamation-triangle me-2"></i>
    This transaction was due on <strong>{{ $recurring->next_due_date->format('M d, Y') }}</strong> and has not been generated yet.
    Use the "Generate Now" button below to create it manually.
</div>
@endif

<div class="row g-4">
    <!-- Left: detail cards -->
    <div class="col-lg-8">

        <!-- Details -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Details</h5>
                @if($recurring->type === 'expense')
                <span class="badge bg-rose">Expense</span>
                @else
                <span class="badge bg-emerald">Income</span>
                @endif
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="text-muted small">Amount</div>
                        <div class="fw-bold fs-5">{{ $currencySymbol }}{{ number_format($recurring->amount, 2) }}</div>
                    </div>
                    <div class="col-sm-6">
                        <div class="text-muted small">Frequency</div>
                        <div class="fw-semibold">{{ $recurring->frequencyLabel() }}</div>
                    </div>
                    <div class="col-sm-6">
                        <div class="text-muted small">Start Date</div>
                        <div>{{ $recurring->start_date->format('M d, Y') }}</div>
                    </div>
                    <div class="col-sm-6">
                        <div class="text-muted small">End Date</div>
                        <div>{{ $recurring->end_date ? $recurring->end_date->format('M d, Y') : '—' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <div class="text-muted small">Category</div>
                        <div>{{ $recurring->category?->name ?? '—' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <div class="text-muted small">Payment Method</div>
                        <div>{{ ucwords(str_replace('_', ' ', $recurring->payment_method)) }}</div>
                    </div>
                    @if($recurring->type === 'expense')
                    <div class="col-sm-6">
                        <div class="text-muted small">Vendor / Payee</div>
                        <div>{{ $recurring->vendor_name ?? '—' }}</div>
                    </div>
                    @else
                    <div class="col-sm-6">
                        <div class="text-muted small">Client</div>
                        <div>
                            @if($recurring->client)
                            <a href="{{ route('clients.show', $recurring->client) }}" class="text-decoration-none">
                                {{ $recurring->client->name }}
                            </a>
                            @else
                            —
                            @endif
                        </div>
                    </div>
                    @endif
                    <div class="col-sm-6">
                        <div class="text-muted small">Project</div>
                        <div>
                            @if($recurring->project)
                            <a href="{{ route('projects.show', $recurring->project) }}" class="text-decoration-none">
                                {{ $recurring->project->title }}
                            </a>
                            @else
                            —
                            @endif
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="text-muted small">Reference Number</div>
                        <div>{{ $recurring->reference_number ?? '—' }}</div>
                    </div>
                    @if($recurring->notes)
                    <div class="col-12">
                        <div class="text-muted small">Notes</div>
                        <div class="bg-light rounded p-2 mt-1">{{ $recurring->notes }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Generation History summary -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">Generation Status</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="text-muted small">Last Generated</div>
                        <div class="fw-semibold">
                            {{ $recurring->last_generated_at ? $recurring->last_generated_at->format('M d, Y \a\t H:i') : 'Never' }}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="text-muted small">Next Due Date</div>
                        <div class="fw-semibold {{ $recurring->isOverdue() ? 'text-danger' : ($recurring->isDueToday() ? 'text-warning' : '') }}">
                            {{ $recurring->next_due_date->format('M d, Y') }}
                            @if($recurring->isOverdue()) <span class="badge bg-danger ms-1">Overdue</span>@endif
                            @if($recurring->isDueToday()) <span class="badge bg-warning text-dark ms-1">Today</span>@endif
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="text-muted small">Status</div>
                        <div>
                            @if($recurring->is_active)
                            <span class="badge bg-success">Active</span>
                            @else
                            <span class="badge bg-secondary">Paused</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="text-muted small">Auto-generated records are tagged</div>
                        <div class="small text-muted"><code>[Auto] {{ $recurring->name }}</code></div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Right: Quick Actions -->
    <div class="col-lg-4">

        <div class="card border-0 shadow-sm mb-4 sticky-top" style="top:1.5rem">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">Quick Actions</h5>
            </div>
            <div class="card-body d-grid gap-2">

                <!-- Generate Now -->
                @if($recurring->is_active)
                <form action="{{ route('recurring.generateNow', $recurring) }}" method="POST"
                      onsubmit="return confirm('Generate a {{ $recurring->type }} record right now for {{ $currencySymbol }}{{ number_format($recurring->amount, 2) }}?')">
                    @csrf
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-bolt me-2"></i> Generate Now
                    </button>
                </form>
                @endif

                <!-- Toggle pause/resume -->
                <form action="{{ route('recurring.toggle', $recurring) }}" method="POST">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn w-100 {{ $recurring->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}">
                        @if($recurring->is_active)
                        <i class="fas fa-pause me-2"></i> Pause Schedule
                        @else
                        <i class="fas fa-play me-2"></i> Resume Schedule
                        @endif
                    </button>
                </form>

                <a href="{{ route('recurring.edit', $recurring) }}" class="btn btn-outline-secondary">
                    <i class="fas fa-edit me-2"></i> Edit Details
                </a>

            </div>
        </div>

        <!-- Danger Zone -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 text-danger">Danger Zone</h5>
            </div>
            <div class="card-body">
                <p class="small text-muted mb-3">Deleting this schedule will not remove already-generated expense or income records.</p>
                <form action="{{ route('recurring.destroy', $recurring) }}" method="POST"
                      onsubmit="return confirm('Delete \'{{ $recurring->name }}\'? Previously generated records are kept.')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger w-100">
                        <i class="fas fa-trash me-2"></i> Delete Schedule
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection
