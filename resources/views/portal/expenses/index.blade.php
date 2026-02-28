@extends('layouts.portal')

@section('title', 'Expenses — FinTrack')

@section('content')

{{-- ── Page Header ─────────────────────────────────── --}}
<div class="page-header">
    <div>
        <h1 class="page-title">Expenses</h1>
        <p class="page-subtitle">Track and manage all your spending</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('expense.categories.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-tags me-1"></i> Categories & Budgets
        </a>
        <a href="{{ route('expenses.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus me-1"></i> Add Expense
        </a>
    </div>
</div>

{{-- ── Budget Overview ──────────────────────────────── --}}
@if($budgetCategories->isNotEmpty())
<div class="card mb-4">
    <div class="card-header">
        <h6 class="card-header-title">
            <span class="card-header-icon" style="background:rgba(245,158,11,.12);">
                <i class="fas fa-gauge-high" style="color:var(--ft-amber);"></i>
            </span>
            Monthly Budget Tracker — {{ $currentMonth }}
        </h6>
        <a href="{{ route('expense.categories.index') }}" class="btn btn-xs btn-outline-secondary">
            Manage budgets <i class="fas fa-arrow-right"></i>
        </a>
    </div>
    <div class="card-body" style="padding:18px 20px;">
        <div class="row g-3">
            @foreach($budgetCategories as $bcat)
            @php
                $spent   = $bcat->spent_this_month ?? 0;
                $budget  = (float) $bcat->monthly_budget;
                $rawPct  = round(($spent / $budget) * 100, 1);
                $pct     = min($rawPct, 100);
                $status  = $rawPct >= 100 ? 'over' : ($rawPct >= 80 ? 'warning' : 'ok');
                $barClr  = $status === 'over' ? 'var(--ft-rose)' : ($status === 'warning' ? 'var(--ft-amber)' : 'var(--ft-emerald)');
                $dotClr  = $bcat->color ?: '#6B7280';
            @endphp
            <div class="col-12 col-sm-6 col-xl-4">
                <div style="padding:12px 14px;border-radius:var(--r-md);background:var(--bg-page);border:1px solid var(--border);">
                    {{-- Category name + status icon --}}
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="d-flex align-items-center gap-2" style="min-width:0;">
                            <span style="width:8px;height:8px;border-radius:50%;background:{{ $dotClr }};flex-shrink:0;display:inline-block;"></span>
                            <span style="font-size:13px;font-weight:700;color:var(--text-h);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                {{ $bcat->name }}
                            </span>
                        </div>
                        @if($status === 'over')
                            <i class="fas fa-triangle-exclamation" style="color:var(--ft-rose);font-size:13px;" title="Over budget"></i>
                        @elseif($status === 'warning')
                            <i class="fas fa-exclamation-circle" style="color:var(--ft-amber);font-size:13px;" title="Nearing limit"></i>
                        @else
                            <i class="fas fa-circle-check" style="color:var(--ft-emerald);font-size:13px;" title="On track"></i>
                        @endif
                    </div>

                    {{-- Progress bar --}}
                    <div class="progress mb-2" style="height:6px;border-radius:99px;background:var(--border);">
                        <div class="progress-bar"
                             role="progressbar"
                             style="width:{{ $pct }}%;background:{{ $barClr }};border-radius:99px;"
                             aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>

                    {{-- Numbers --}}
                    <div class="d-flex justify-content-between" style="font-size:11.5px;">
                        <span style="color:{{ $barClr }};font-weight:700;">{{ $rawPct }}%</span>
                        <span style="color:var(--text-muted);">
                            {{ $currencySymbol }}{{ number_format($spent, 2) }}
                            <span style="color:var(--text-faint);">/</span>
                            {{ $currencySymbol }}{{ number_format($budget, 2) }}
                        </span>
                    </div>

                    @if($status === 'over')
                    <div style="font-size:11px;color:var(--ft-rose);margin-top:4px;font-weight:600;">
                        <i class="fas fa-arrow-up me-1"></i>{{ $currencySymbol }}{{ number_format($spent - $budget, 2) }} over budget
                    </div>
                    @elseif($status === 'warning')
                    <div style="font-size:11px;color:#B45309;margin-top:4px;">
                        {{ $currencySymbol }}{{ number_format($budget - $spent, 2) }} remaining
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

{{-- ── Filters ──────────────────────────────────────── --}}
<div class="card mb-4">
    <div class="card-body" style="padding:16px 20px;">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-12 col-md-3">
                <input type="text" name="search" class="form-control form-control-sm"
                       placeholder="Search vendor, notes, ref…" value="{{ $search }}">
            </div>
            <div class="col-6 col-md-2">
                <select name="category_id" class="form-select form-select-sm">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ $category_id == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-2">
                <select name="income_id" class="form-select form-select-sm">
                    <option value="">All Income Sources</option>
                    @foreach($incomes as $income)
                    <option value="{{ $income->id }}" {{ $income_id == $income->id ? 'selected' : '' }}>
                        {{ number_format($income->amount, 2) }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-2">
                <input type="date" name="date_from" class="form-control form-control-sm" value="{{ $date_from }}">
            </div>
            <div class="col-6 col-md-2">
                <input type="date" name="date_to" class="form-control form-control-sm" value="{{ $date_to }}">
            </div>
            <div class="col-12 col-md-1 d-flex gap-1">
                <button type="submit" class="btn btn-primary btn-sm flex-fill">
                    <i class="fas fa-search"></i>
                </button>
                @if($search || $category_id || $income_id || $date_from || $date_to)
                <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary btn-sm" title="Clear filters">
                    <i class="fas fa-xmark"></i>
                </a>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- ── Expenses Table ───────────────────────────────── --}}
<div class="card">
    <div class="card-body p-0">
        @if($expenses->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Category</th>
                        <th>Vendor</th>
                        <th>Amount</th>
                        <th>Payment</th>
                        <th>Reference</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($expenses as $expense)
                    @php
                        // Inline budget warning for this expense's category this month
                        $showBudgetWarn = false;
                        if ($expense->category && $expense->category->monthly_budget > 0) {
                            $catBudget = $budgetCategories->firstWhere('id', $expense->category_id);
                            if ($catBudget) {
                                $catPct = ($catBudget->spent_this_month / $catBudget->monthly_budget) * 100;
                                $showBudgetWarn = $catPct >= 80;
                                $catOver = $catPct >= 100;
                            }
                        }
                    @endphp
                    <tr>
                        <td style="white-space:nowrap;font-size:13px;">
                            {{ $expense->expense_date->format('M d, Y') }}
                        </td>
                        <td>
                            @if($expense->category)
                            <div class="d-flex align-items-center gap-1">
                                <span class="badge" style="background-color:{{ $expense->category->color ?? '#6B7280' }};color:#fff;font-size:11.5px;">
                                    {{ $expense->category->name }}
                                </span>
                                @if($showBudgetWarn)
                                <i class="fas fa-{{ $catOver ? 'triangle-exclamation' : 'exclamation-circle' }}"
                                   style="color:{{ $catOver ? 'var(--ft-rose)' : 'var(--ft-amber)' }};font-size:11px;"
                                   title="{{ $catOver ? 'Category over budget this month' : 'Category nearing budget limit' }}"></i>
                                @endif
                            </div>
                            @else
                            <span style="color:var(--text-faint);font-size:13px;">—</span>
                            @endif
                        </td>
                        <td style="font-size:13px;">{{ $expense->vendor_name ?: '—' }}</td>
                        <td>
                            <span style="font-size:13px;font-weight:700;color:var(--ft-rose);">
                                {{ $currencySymbol }}{{ number_format($expense->amount, 2) }}
                            </span>
                        </td>
                        <td>
                            @switch($expense->payment_method)
                            @case('cash')
                                <span class="badge" style="background:rgba(14,116,144,.12);color:var(--ft-teal);">Cash</span>
                                @break
                            @case('bank_transfer')
                                <span class="badge" style="background:rgba(11,42,74,.10);color:var(--ft-navy);">Bank Transfer</span>
                                @break
                            @case('mpesa')
                                <span class="badge" style="background:rgba(34,197,94,.12);color:#15803D;">M-Pesa</span>
                                @break
                            @case('card')
                                <span class="badge" style="background:rgba(139,92,246,.12);color:#7C3AED;">Card</span>
                                @break
                            @default
                                <span class="badge" style="background:var(--bg-page);color:var(--text-muted);">{{ ucfirst($expense->payment_method) }}</span>
                            @endswitch
                        </td>
                        <td style="font-size:12px;color:var(--text-muted);">{{ $expense->reference_number ?: '—' }}</td>
                        <td class="text-end">
                            <div class="dropdown">
                                <button class="btn btn-xs btn-light" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('expenses.show', $expense->id) }}">
                                            <i class="fas fa-eye me-2"></i> View
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('expenses.edit', $expense->id) }}">
                                            <i class="fas fa-pen me-2"></i> Edit
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('expenses.destroy', $expense->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger btn-delete">
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
        @else
        <div class="empty-state">
            <div class="empty-state-icon">
                <i class="fas fa-receipt"></i>
            </div>
            <p class="mb-1" style="font-weight:600;">No expense records found</p>
            <p class="mb-3" style="font-size:13px;color:var(--text-muted);">
                @if($search || $category_id || $income_id || $date_from || $date_to)
                    Try adjusting your filters.
                @else
                    Start tracking your expenses by recording your first entry.
                @endif
            </p>
            @if(!$search && !$category_id && !$income_id && !$date_from && !$date_to)
            <a href="{{ route('expenses.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus me-1"></i> Add Expense
            </a>
            @else
            <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-xmark me-1"></i> Clear Filters
            </a>
            @endif
        </div>
        @endif
    </div>
</div>

@if($expenses->hasPages())
<div class="mt-4">
    {{ $expenses->links() }}
</div>
@endif

@endsection
