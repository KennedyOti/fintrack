@extends('layouts.portal')

@section('title', 'Recurring Transactions - FinTrack')

@section('content')
<!-- Page Header -->
<div class="page-header mb-4">
    <div>
        <h4 class="page-title">Recurring Transactions</h4>
        <p class="page-subtitle">Auto-generate expenses and income on a schedule</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('recurring.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> New Recurring
        </a>
    </div>
</div>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm stat-card sc-teal">
            <div class="card-body py-3">
                <div class="sc-label">Active</div>
                <div class="sc-value">{{ $totalActive }}</div>
                <div class="sc-sub">running schedules</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm stat-card sc-rose">
            <div class="card-body py-3">
                <div class="sc-label">Due Today</div>
                <div class="sc-value">{{ $dueToday }}</div>
                <div class="sc-sub">pending generation</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm stat-card sc-amber">
            <div class="card-body py-3">
                <div class="sc-label">Due This Week</div>
                <div class="sc-value">{{ $dueThisWeek }}</div>
                <div class="sc-sub">coming up</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm stat-card sc-navy">
            <div class="card-body py-3">
                <div class="sc-label">Paused</div>
                <div class="sc-value">{{ $totalPaused }}</div>
                <div class="sc-sub">inactive schedules</div>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('recurring.index') }}" class="row g-2 align-items-end">
            <div class="col-sm-4 col-md-3">
                <label class="form-label form-label-sm">Type</label>
                <select name="type" class="form-select form-select-sm">
                    <option value="">All Types</option>
                    <option value="expense" {{ $type === 'expense' ? 'selected' : '' }}>Expense</option>
                    <option value="income"  {{ $type === 'income'  ? 'selected' : '' }}>Income</option>
                </select>
            </div>
            <div class="col-sm-4 col-md-3">
                <label class="form-label form-label-sm">Frequency</label>
                <select name="frequency" class="form-select form-select-sm">
                    <option value="">All Frequencies</option>
                    <option value="daily"     {{ $frequency === 'daily'     ? 'selected' : '' }}>Daily</option>
                    <option value="weekly"    {{ $frequency === 'weekly'    ? 'selected' : '' }}>Weekly</option>
                    <option value="monthly"   {{ $frequency === 'monthly'   ? 'selected' : '' }}>Monthly</option>
                    <option value="quarterly" {{ $frequency === 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                    <option value="yearly"    {{ $frequency === 'yearly'    ? 'selected' : '' }}>Yearly</option>
                </select>
            </div>
            <div class="col-sm-4 col-md-3">
                <label class="form-label form-label-sm">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Statuses</option>
                    <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="paused" {{ $status === 'paused' ? 'selected' : '' }}>Paused</option>
                </select>
            </div>
            <div class="col-sm-12 col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm flex-grow-1">Filter</button>
                @if($type || $frequency || $status)
                <a href="{{ route('recurring.index') }}" class="btn btn-outline-secondary btn-sm">Clear</a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Table -->
@if($recurring->count())
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Name</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Frequency</th>
                        <th>Next Due</th>
                        <th>Status</th>
                        <th>Last Generated</th>
                        <th class="pe-4 text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recurring as $item)
                    @php
                        $isDue    = $item->is_active && $item->next_due_date->lte(\Carbon\Carbon::today());
                        $isOverdue = $item->is_active && $item->next_due_date->lt(\Carbon\Carbon::today());
                    @endphp
                    <tr>
                        <td class="ps-4">
                            <a href="{{ route('recurring.show', $item) }}" class="fw-semibold text-decoration-none">
                                {{ $item->name }}
                            </a>
                            @if($item->category)
                            <div class="small text-muted">{{ $item->category->name }}</div>
                            @endif
                        </td>
                        <td>
                            @if($item->type === 'expense')
                            <span class="badge bg-danger">Expense</span>
                            @else
                            <span class="badge bg-success">Income</span>
                            @endif
                        </td>
                        <td class="fw-semibold">{{ $currencySymbol }}{{ number_format($item->amount, 2) }}</td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ $item->frequencyLabel() }}</span>
                        </td>
                        <td>
                            <span class="{{ $isOverdue ? 'text-danger fw-semibold' : ($isDue ? 'text-warning fw-semibold' : '') }}">
                                {{ $item->next_due_date->format('M d, Y') }}
                                @if($isOverdue) <i class="fas fa-exclamation-circle ms-1"></i>@endif
                            </span>
                        </td>
                        <td>
                            @if($item->is_active)
                            <span class="badge bg-success">Active</span>
                            @else
                            <span class="badge bg-secondary">Paused</span>
                            @endif
                        </td>
                        <td class="text-muted small">
                            {{ $item->last_generated_at ? $item->last_generated_at->format('M d, Y') : '—' }}
                        </td>
                        <td class="pe-4 text-end">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                    Actions
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('recurring.show', $item) }}">
                                            <i class="fas fa-eye me-2 text-muted"></i> View
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('recurring.edit', $item) }}">
                                            <i class="fas fa-edit me-2 text-muted"></i> Edit
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('recurring.toggle', $item) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="dropdown-item">
                                                @if($item->is_active)
                                                <i class="fas fa-pause me-2 text-warning"></i> Pause
                                                @else
                                                <i class="fas fa-play me-2 text-success"></i> Resume
                                                @endif
                                            </button>
                                        </form>
                                    </li>
                                    <li>
                                        <form action="{{ route('recurring.destroy', $item) }}" method="POST"
                                              onsubmit="return confirm('Delete \'{{ $item->name }}\'? This will not remove already-generated records.')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="fas fa-trash me-2"></i> Delete
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
    </div>
</div>

<div class="mt-3">
    {{ $recurring->withQueryString()->links() }}
</div>
@else
<div class="card border-0 shadow-sm">
    <div class="card-body text-center py-5">
        <i class="fas fa-sync-alt fa-3x text-muted mb-3"></i>
        <h5 class="text-muted">No recurring transactions yet</h5>
        <p class="text-muted small mb-4">Set up automatic expenses like subscriptions, rent, and retainer income.</p>
        <a href="{{ route('recurring.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Create First Recurring Transaction
        </a>
    </div>
</div>
@endif
@endsection
