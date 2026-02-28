@extends('layouts.portal')

@section('title', 'Expense Categories — FinTrack')

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title">Expense Categories</h1>
        <p class="page-subtitle">Budgets tracked for <strong>{{ $currentMonth }}</strong></p>
    </div>
    <div class="page-actions">
        <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Expenses
        </a>
        <a href="{{ route('expense.categories.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus me-1"></i> Add Category
        </a>
    </div>
</div>

@if($categories->count() > 0)

{{-- ── Legend strip ─────────────────────────────── --}}
<div class="d-flex align-items-center gap-3 mb-3" style="font-size:12px;color:var(--text-muted);">
    <span><i class="fas fa-circle" style="color:var(--ft-emerald);font-size:9px;"></i> On track (&lt; 80 %)</span>
    <span><i class="fas fa-circle" style="color:var(--ft-amber);font-size:9px;"></i> Warning (80 – 99 %)</span>
    <span><i class="fas fa-circle" style="color:var(--ft-rose);font-size:9px;"></i> Over budget (≥ 100 %)</span>
    <span><i class="fas fa-circle" style="color:var(--border);font-size:9px;"></i> No budget set</span>
</div>

{{-- ── Category cards ───────────────────────────── --}}
<div class="row g-3">
    @foreach($categories as $cat)
    @php
        $spent  = $cat->spent_this_month ?? 0;
        $budget = $cat->monthly_budget;
        $hasBudget = $budget && $budget > 0;

        if ($hasBudget) {
            $pct    = min(round(($spent / $budget) * 100, 1), 100);
            $rawPct = round(($spent / $budget) * 100, 1);
            $status = $rawPct >= 100 ? 'over' : ($rawPct >= 80 ? 'warning' : 'ok');
        } else {
            $pct    = 0;
            $rawPct = 0;
            $status = null;
        }

        $barColor = match($status) {
            'over'    => 'var(--ft-rose)',
            'warning' => 'var(--ft-amber)',
            'ok'      => 'var(--ft-emerald)',
            default   => 'var(--text-faint)',
        };

        $dotColor = $cat->color ?: '#6B7280';
    @endphp
    <div class="col-12 col-md-6 col-xl-4">
        <div class="card h-100" style="border-left:3px solid {{ $dotColor }};">
            <div class="card-body" style="padding:16px 18px;">

                {{-- Header row --}}
                <div class="d-flex align-items-start justify-content-between gap-2 mb-3">
                    <div class="d-flex align-items-center gap-2" style="min-width:0;">
                        <span style="width:10px;height:10px;border-radius:50%;background:{{ $dotColor }};flex-shrink:0;display:inline-block;"></span>
                        <span style="font-size:14px;font-weight:700;color:var(--text-h);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                            {{ $cat->name }}
                        </span>
                    </div>
                    <div class="d-flex align-items-center gap-1 flex-shrink-0">
                        @if($status === 'over')
                            <span class="badge" style="background:rgba(244,63,94,.12);color:var(--ft-rose);font-size:10.5px;">
                                <i class="fas fa-triangle-exclamation me-1"></i>Over Budget
                            </span>
                        @elseif($status === 'warning')
                            <span class="badge" style="background:rgba(245,158,11,.12);color:#B45309;font-size:10.5px;">
                                <i class="fas fa-exclamation-circle me-1"></i>Near Limit
                            </span>
                        @elseif($status === 'ok')
                            <span class="badge" style="background:rgba(34,197,94,.10);color:#15803D;font-size:10.5px;">
                                <i class="fas fa-circle-check me-1"></i>On Track
                            </span>
                        @else
                            <span class="badge" style="background:var(--bg-page);color:var(--text-muted);font-size:10.5px;">
                                No Budget
                            </span>
                        @endif

                        {{-- Actions dropdown --}}
                        <div class="dropdown ms-1">
                            <button class="btn btn-xs btn-light" data-bs-toggle="dropdown" aria-expanded="false"
                                    style="width:26px;height:26px;padding:0;display:flex;align-items:center;justify-content:center;">
                                <i class="fas fa-ellipsis-v" style="font-size:11px;"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="{{ route('expense.categories.edit', $cat->id) }}">
                                        <i class="fas fa-pen me-2"></i> Edit
                                    </a>
                                </li>
                                <li>
                                    <form action="{{ route('expense.categories.destroy', $cat->id) }}" method="POST"
                                          onsubmit="return confirm('Delete category \'{{ addslashes($cat->name) }}\'? This cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="fas fa-trash me-2"></i> Delete
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                @if($hasBudget)
                {{-- Spent vs budget numbers --}}
                <div class="d-flex justify-content-between align-items-baseline mb-1">
                    <span style="font-size:18px;font-weight:800;color:{{ $status === 'over' ? 'var(--ft-rose)' : 'var(--text-h)' }};">
                        {{ $currencySymbol }}{{ number_format($spent, 2) }}
                    </span>
                    <span style="font-size:12px;color:var(--text-muted);">
                        of {{ $currencySymbol }}{{ number_format($budget, 2) }}
                    </span>
                </div>

                {{-- Progress bar --}}
                <div class="progress mb-2" style="height:7px;border-radius:99px;background:var(--bg-page);">
                    <div class="progress-bar"
                         role="progressbar"
                         style="width:{{ $pct }}%;background:{{ $barColor }};border-radius:99px;transition:width .4s ease;"
                         aria-valuenow="{{ $pct }}"
                         aria-valuemin="0"
                         aria-valuemax="100"></div>
                </div>

                {{-- Percentage + remaining --}}
                <div class="d-flex justify-content-between" style="font-size:11.5px;color:var(--text-muted);">
                    <span style="font-weight:600;color:{{ $barColor }};">{{ $rawPct }}%</span>
                    @if($status === 'over')
                        <span style="color:var(--ft-rose);font-weight:600;">
                            <i class="fas fa-arrow-up me-1"></i>{{ $currencySymbol }}{{ number_format($spent - $budget, 2) }} over
                        </span>
                    @else
                        <span>{{ $currencySymbol }}{{ number_format($budget - $spent, 2) }} remaining</span>
                    @endif
                </div>

                @else
                {{-- No budget: show total spent this month --}}
                <div style="font-size:13px;color:var(--text-muted);">
                    Spent this month:
                    <strong style="color:var(--text-h);">{{ $currencySymbol }}{{ number_format($spent, 2) }}</strong>
                </div>
                <div class="mt-2">
                    <a href="{{ route('expense.categories.edit', $cat->id) }}"
                       style="font-size:12px;color:var(--ft-teal);text-decoration:none;">
                        <i class="fas fa-plus-circle me-1"></i>Set a monthly budget
                    </a>
                </div>
                @endif

            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- Pagination --}}
@if($categories->hasPages())
<div class="d-flex justify-content-center mt-4">
    {{ $categories->links() }}
</div>
@endif

@else
{{-- Empty state --}}
<div class="card">
    <div class="card-body">
        <div class="empty-state">
            <div class="empty-state-icon">
                <i class="fas fa-tags"></i>
            </div>
            <p class="mb-1" style="font-weight:600;">No expense categories yet</p>
            <p class="mb-3" style="font-size:13px;color:var(--text-muted);">
                Create categories to organise your expenses and set monthly spending budgets.
            </p>
            <a href="{{ route('expense.categories.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus me-1"></i> Create Your First Category
            </a>
        </div>
    </div>
</div>
@endif

@endsection
