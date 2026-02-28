@extends('layouts.portal')

@section('title', 'Dashboard — FinTrack')

@section('content')

{{-- ── Page Header ─────────────────────────────────── --}}
<div class="page-header">
    <div>
        <h1 class="page-title">Dashboard</h1>
        <p class="page-subtitle">
            Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 17 ? 'afternoon' : 'evening') }},
            {{ Str::words(Auth::user()->name, 1, '') }}
        </p>
    </div>
    <div class="page-actions">
        <span style="font-size:12px;color:var(--text-muted);font-weight:500;">
            <i class="fas fa-calendar-days me-1" style="color:var(--ft-teal);"></i>
            {{ now()->format('M j, Y') }}
        </span>
        {{-- Currency Selector --}}
        <div class="dropdown">
            <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <i class="fas fa-coins"></i>
                {{ $currencySymbol }} {{ $currencyCode }}
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                @foreach(['USD','EUR','GBP','KES','NGN','ZAR','INR','JPY'] as $code)
                <li>
                    <a class="dropdown-item {{ $currencyCode == $code ? 'fw-semibold' : '' }}" href="#"
                       onclick="event.preventDefault(); document.getElementById('currency-form-{{ $code }}').submit();">
                        @if($code=='USD')$
                        @elseif($code=='EUR')€
                        @elseif($code=='GBP')£
                        @elseif($code=='KES')KSh
                        @elseif($code=='NGN')₦
                        @elseif($code=='ZAR')R
                        @elseif($code=='INR')₹
                        @elseif($code=='JPY')¥
                        @else{{ $code }}@endif {{ $code }}
                        @if($currencyCode==$code)<i class="fas fa-check ms-auto" style="color:#059669;font-size:10px;"></i>@endif
                    </a>
                    <form id="currency-form-{{ $code }}" action="{{ route('settings.currency.update') }}" method="POST" class="d-none">
                        @csrf
                        <input type="hidden" name="currency_code" value="{{ $code }}">
                    </form>
                </li>
                @endforeach
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item" href="{{ route('settings.index') }}">
                        <i class="fas fa-gear"></i> More Settings
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>

{{-- ── Stat Cards ───────────────────────────────────── --}}
<div class="row g-3 mb-4">

    {{-- Net Position --}}
    <div class="col-6 col-xl-3">
        <div class="card stat-card {{ $netPosition >= 0 ? 'sc-navy' : 'sc-rose' }}">
            <div class="d-flex justify-content-between align-items-start">
                <div style="min-width:0;flex:1;">
                    <div class="stat-label">Net Position</div>
                    <div class="stat-value">{{ $currencySymbol }}{{ number_format(abs($netPosition), 0) }}</div>
                    <div class="stat-sub">
                        <i class="fas fa-{{ $netPosition >= 0 ? 'arrow-up' : 'arrow-down' }}"></i>
                        {{ $netPosition >= 0 ? 'Positive' : 'Negative' }}
                    </div>
                </div>
                <div class="stat-badge">
                    <i class="fas fa-wallet"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Total Income --}}
    <div class="col-6 col-xl-3">
        <div class="card stat-card sc-emerald">
            <div class="d-flex justify-content-between align-items-start">
                <div style="min-width:0;flex:1;">
                    <div class="stat-label">Total Income</div>
                    <div class="stat-value">{{ $currencySymbol }}{{ number_format($totalIncome, 0) }}</div>
                    <div class="stat-sub">
                        <i class="fas fa-arrow-trend-up"></i> This year
                    </div>
                </div>
                <div class="stat-badge">
                    <i class="fas fa-arrow-trend-up"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Total Expenses --}}
    <div class="col-6 col-xl-3">
        <div class="card stat-card sc-rose">
            <div class="d-flex justify-content-between align-items-start">
                <div style="min-width:0;flex:1;">
                    <div class="stat-label">Total Expenses</div>
                    <div class="stat-value">{{ $currencySymbol }}{{ number_format($totalExpenses, 0) }}</div>
                    <div class="stat-sub">
                        <i class="fas fa-arrow-trend-down"></i> This year
                    </div>
                </div>
                <div class="stat-badge">
                    <i class="fas fa-arrow-trend-down"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Savings --}}
    <div class="col-6 col-xl-3">
        <div class="card stat-card sc-teal">
            <div class="d-flex justify-content-between align-items-start">
                <div style="min-width:0;flex:1;">
                    <div class="stat-label">Savings</div>
                    <div class="stat-value">{{ $currencySymbol }}{{ number_format($totalSavings, 0) }}</div>
                    <div class="stat-sub">
                        <i class="fas fa-piggy-bank"></i> All accounts
                    </div>
                </div>
                <div class="stat-badge">
                    <i class="fas fa-piggy-bank"></i>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- ── Charts ───────────────────────────────────────── --}}
<div class="row g-3 mb-4">

    {{-- Income vs Expenses --}}
    <div class="col-12 col-lg-8">
        <div class="card h-100">
            <div class="card-header">
                <h6 class="card-header-title">
                    <span class="card-header-icon bg-navy-soft">
                        <i class="fas fa-chart-column text-navy"></i>
                    </span>
                    Income vs Expenses
                </h6>
                <span class="badge bg-secondary">Last 6 months</span>
            </div>
            <div class="card-body">
                <canvas id="incomeExpenseChart" style="max-height:220px;"></canvas>
            </div>
        </div>
    </div>

    {{-- Expenses by Category --}}
    <div class="col-12 col-lg-4">
        <div class="card h-100">
            <div class="card-header">
                <h6 class="card-header-title">
                    <span class="card-header-icon bg-teal-soft">
                        <i class="fas fa-chart-pie text-teal"></i>
                    </span>
                    By Category
                </h6>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <canvas id="expenseCategoryChart" style="max-height:200px;max-width:100%;"></canvas>
            </div>
        </div>
    </div>

</div>

{{-- ── Quick Stats Row ──────────────────────────────── --}}
<div class="row g-3 mb-4">

    <div class="col-4">
        <div class="card">
            <div class="card-body" style="padding:13px 15px!important;">
                <div class="d-flex align-items-center gap-3">
                    <div class="card-header-icon bg-amber-soft" style="width:38px;height:38px;border-radius:10px;flex-shrink:0;">
                        <i class="fas fa-diagram-project text-amber" style="font-size:13px;color:var(--ft-amber);"></i>
                    </div>
                    <div style="min-width:0;">
                        <div style="font-size:10.5px;color:var(--text-muted);font-weight:700;text-transform:uppercase;letter-spacing:.4px;">Projects</div>
                        <div style="font-size:22px;font-weight:800;color:var(--text-h);line-height:1.1;">{{ $activeProjects }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-4">
        <div class="card">
            <div class="card-body" style="padding:13px 15px!important;">
                <div class="d-flex align-items-center gap-3">
                    <div class="card-header-icon bg-teal-soft" style="width:38px;height:38px;border-radius:10px;flex-shrink:0;">
                        <i class="fas fa-users" style="font-size:13px;color:var(--ft-teal);"></i>
                    </div>
                    <div style="min-width:0;">
                        <div style="font-size:10.5px;color:var(--text-muted);font-weight:700;text-transform:uppercase;letter-spacing:.4px;">Clients</div>
                        <div style="font-size:22px;font-weight:800;color:var(--text-h);line-height:1.1;">{{ $totalClients }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-4">
        <div class="card">
            <div class="card-body" style="padding:13px 15px!important;">
                <div class="d-flex align-items-center gap-3">
                    <div class="card-header-icon bg-rose-soft" style="width:38px;height:38px;border-radius:10px;flex-shrink:0;">
                        <i class="fas fa-file-invoice-dollar" style="font-size:13px;color:var(--ft-rose);"></i>
                    </div>
                    <div style="min-width:0;">
                        <div style="font-size:10.5px;color:var(--text-muted);font-weight:700;text-transform:uppercase;letter-spacing:.4px;">Pending</div>
                        <div style="font-size:22px;font-weight:800;color:var(--text-h);line-height:1.1;">{{ $pendingInvoices }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- ── Budget Alerts ────────────────────────────────── --}}
@if($budgetAlerts->isNotEmpty())
<div class="card mb-4" style="border-left:3px solid var(--ft-amber);">
    <div class="card-header">
        <h6 class="card-header-title">
            <span class="card-header-icon" style="background:rgba(245,158,11,.12);">
                <i class="fas fa-gauge-high" style="color:var(--ft-amber);"></i>
            </span>
            Budget Alerts — {{ now()->format('F Y') }}
        </h6>
        <a href="{{ route('expense.categories.index') }}" class="btn btn-xs btn-outline-secondary">
            View all <i class="fas fa-arrow-right"></i>
        </a>
    </div>
    <div class="card-body" style="padding:16px 20px;">
        <div class="row g-3">
            @foreach($budgetAlerts as $bcat)
            @php
                $spent  = $bcat->spent_this_month;
                $budget = (float) $bcat->monthly_budget;
                $rawPct = round(($spent / $budget) * 100, 1);
                $pct    = min($rawPct, 100);
                $over   = $rawPct >= 100;
                $barClr = $over ? 'var(--ft-rose)' : 'var(--ft-amber)';
                $dotClr = $bcat->color ?: '#6B7280';
            @endphp
            <div class="col-12 col-sm-6 col-xl-4">
                <div style="padding:12px 14px;border-radius:var(--r-md);background:{{ $over ? 'rgba(244,63,94,.05)' : 'rgba(245,158,11,.05)' }};border:1px solid {{ $over ? 'rgba(244,63,94,.18)' : 'rgba(245,158,11,.20)' }};">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="d-flex align-items-center gap-2" style="min-width:0;">
                            <span style="width:8px;height:8px;border-radius:50%;background:{{ $dotClr }};flex-shrink:0;display:inline-block;"></span>
                            <span style="font-size:13px;font-weight:700;color:var(--text-h);">{{ $bcat->name }}</span>
                        </div>
                        @if($over)
                            <span class="badge" style="background:rgba(244,63,94,.12);color:var(--ft-rose);font-size:10px;">
                                <i class="fas fa-triangle-exclamation me-1"></i>Over Budget
                            </span>
                        @else
                            <span class="badge" style="background:rgba(245,158,11,.12);color:#B45309;font-size:10px;">
                                <i class="fas fa-exclamation-circle me-1"></i>Near Limit
                            </span>
                        @endif
                    </div>
                    <div class="progress mb-2" style="height:6px;border-radius:99px;background:var(--border);">
                        <div class="progress-bar" role="progressbar"
                             style="width:{{ $pct }}%;background:{{ $barClr }};border-radius:99px;"
                             aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="d-flex justify-content-between" style="font-size:11.5px;">
                        <span style="font-weight:700;color:{{ $barClr }};">{{ $rawPct }}%</span>
                        <span style="color:var(--text-muted);">
                            {{ $currencySymbol }}{{ number_format($spent, 2) }} / {{ $currencySymbol }}{{ number_format($budget, 2) }}
                        </span>
                    </div>
                    @if($over)
                    <div style="font-size:11px;color:var(--ft-rose);margin-top:3px;font-weight:600;">
                        <i class="fas fa-arrow-up me-1"></i>{{ $currencySymbol }}{{ number_format($spent - $budget, 2) }} over
                    </div>
                    @else
                    <div style="font-size:11px;color:#B45309;margin-top:3px;">
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

{{-- ── Recent Transactions ──────────────────────────── --}}
<div class="row g-3">

    {{-- Recent Income --}}
    <div class="col-12 col-lg-6">
        <div class="card">
            <div class="card-header">
                <h6 class="card-header-title">
                    <span class="card-header-icon bg-emerald-soft">
                        <i class="fas fa-arrow-trend-up" style="color:#059669;"></i>
                    </span>
                    Recent Income
                </h6>
                <a href="{{ route('income.index') }}" class="btn btn-xs btn-outline-secondary">
                    View all <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            <div class="card-body p-0">
                @if($recentIncomes->count() > 0)
                    @foreach($recentIncomes as $income)
                    <div class="d-flex align-items-center justify-content-between px-3 py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                        <div class="d-flex align-items-center gap-2" style="min-width:0;flex:1;">
                            <div style="width:30px;height:30px;background:rgba(5,150,105,.08);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <i class="fas fa-arrow-up" style="color:#059669;font-size:10px;"></i>
                            </div>
                            <div style="min-width:0;">
                                <div style="font-size:13px;font-weight:600;color:var(--text-h);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                    {{ $income->category ? $income->category->name : 'Uncategorized' }}
                                </div>
                                <div style="font-size:11.5px;color:var(--text-muted);">
                                    {{ $income->client ? $income->client->name : '—' }}
                                </div>
                            </div>
                        </div>
                        <div class="text-end ms-2" style="flex-shrink:0;">
                            <div style="font-size:13px;font-weight:700;color:#059669;">
                                +{{ $currencySymbol }}{{ number_format($income->amount, 2) }}
                            </div>
                            <div style="font-size:11px;color:var(--text-muted);">
                                {{ $income->income_date->format('M d') }}
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="fas fa-inbox"></i>
                    </div>
                    <p class="mb-2">No recent income recorded</p>
                    <a href="{{ route('income.create') }}" class="btn btn-sm btn-success">
                        <i class="fas fa-plus"></i> Add Income
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Recent Expenses --}}
    <div class="col-12 col-lg-6">
        <div class="card">
            <div class="card-header">
                <h6 class="card-header-title">
                    <span class="card-header-icon bg-rose-soft">
                        <i class="fas fa-arrow-trend-down" style="color:var(--ft-rose);"></i>
                    </span>
                    Recent Expenses
                </h6>
                <a href="{{ route('expenses.index') }}" class="btn btn-xs btn-outline-secondary">
                    View all <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            <div class="card-body p-0">
                @if($recentExpenses->count() > 0)
                    @foreach($recentExpenses as $expense)
                    <div class="d-flex align-items-center justify-content-between px-3 py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                        <div class="d-flex align-items-center gap-2" style="min-width:0;flex:1;">
                            <div style="width:30px;height:30px;background:rgba(244,63,94,.07);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <i class="fas fa-arrow-down" style="color:var(--ft-rose);font-size:10px;"></i>
                            </div>
                            <div style="min-width:0;">
                                <div style="font-size:13px;font-weight:600;color:var(--text-h);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                    {{ $expense->category ? $expense->category->name : 'Uncategorized' }}
                                </div>
                                <div style="font-size:11.5px;color:var(--text-muted);">
                                    {{ $expense->vendor_name ?: '—' }}
                                </div>
                            </div>
                        </div>
                        <div class="text-end ms-2" style="flex-shrink:0;">
                            <div style="font-size:13px;font-weight:700;color:var(--ft-rose);">
                                -{{ $currencySymbol }}{{ number_format($expense->amount, 2) }}
                            </div>
                            <div style="font-size:11px;color:var(--text-muted);">
                                {{ $expense->expense_date->format('M d') }}
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="fas fa-inbox"></i>
                    </div>
                    <p class="mb-2">No recent expenses recorded</p>
                    <a href="{{ route('expenses.create') }}" class="btn btn-sm btn-danger">
                        <i class="fas fa-plus"></i> Add Expense
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>

</div>

@endsection

@section('scripts')
<script>
(function() {
    // ── Chart colour palette (matches brand)
    const palette = {
        navy:    '#0B2A4A',
        teal:    '#0E7490',
        cyan:    '#22D3EE',
        emerald: '#059669',
        rose:    '#F43F5E',
        amber:   '#F59E0B',
        violet:  '#8B5CF6',
        sky:     '#0891B2',
    };

    Chart.defaults.font.family = "'Plus Jakarta Sans', 'Segoe UI', system-ui, sans-serif";
    Chart.defaults.font.size   = 12;
    Chart.defaults.color       = '#64748B';

    // ── Income vs Expenses Bar Chart
    const ieCtx = document.getElementById('incomeExpenseChart');
    if (ieCtx) {
        new Chart(ieCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($months) !!},
                datasets: [
                    {
                        label: 'Income',
                        data: {!! json_encode($incomeData) !!},
                        backgroundColor: 'rgba(5,150,105,.82)',
                        borderRadius: 5,
                        borderSkipped: false,
                    },
                    {
                        label: 'Expenses',
                        data: {!! json_encode($expenseData) !!},
                        backgroundColor: 'rgba(244,63,94,.75)',
                        borderRadius: 5,
                        borderSkipped: false,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index' },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: { usePointStyle: true, pointStyle: 'circle', boxWidth: 7, padding: 14 }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#F1F5F9', drawBorder: false },
                        border: { display: false },
                        ticks: {
                            callback: v => '{{ $currencySymbol }}' + (v >= 1000 ? (v/1000).toFixed(0) + 'k' : v)
                        }
                    },
                    x: {
                        grid: { display: false },
                        border: { display: false }
                    }
                }
            }
        });
    }

    // ── Expense Category Doughnut
    const catCtx = document.getElementById('expenseCategoryChart');
    if (catCtx) {
        const catLabels = [];
        const catData   = [];
        const catPalette = [palette.navy, palette.teal, palette.rose, palette.amber, palette.violet, palette.cyan, palette.emerald, palette.sky];

        @foreach($expensesByCategory as $categoryId => $categoryData)
            catLabels.push('{{ addslashes($categoryData['name']) }}');
            catData.push({{ $categoryData['total'] }});
        @endforeach

        new Chart(catCtx, {
            type: 'doughnut',
            data: {
                labels: catLabels.length > 0 ? catLabels : ['No Data'],
                datasets: [{
                    data: catData.length > 0 ? catData : [1],
                    backgroundColor: catLabels.length > 0
                        ? catPalette.slice(0, catLabels.length)
                        : ['#E2E8F0'],
                    borderWidth: 3,
                    borderColor: '#fff',
                    hoverOffset: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '66%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { usePointStyle: true, pointStyle: 'circle', boxWidth: 7, padding: 12, font: { size: 11 } }
                    }
                }
            }
        });
    }
})();
</script>
@endsection
