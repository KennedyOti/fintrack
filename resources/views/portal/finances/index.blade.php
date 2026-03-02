@extends('layouts.portal')

@section('title', 'Financial Overview - FinTrack')

@section('content')
<!-- Page Header -->
<div class="page-header">
    <div>
        <h1 class="page-title">Financial Overview</h1>
        <p class="page-subtitle">Complete financial summary and reports</p>
    </div>
    <div class="page-actions">
        <!-- Currency Display -->
        <div class="dropdown">
            <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <i class="fas fa-coins me-1"></i>
                {{ CurrencyHelper::getSymbol() }} {{ CurrencyHelper::getUserCurrency() }}
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><span class="dropdown-item-text text-muted small">Display Currency</span></li>
                <li><hr class="dropdown-divider"></li>
                @foreach(['USD', 'EUR', 'GBP', 'KES', 'NGN', 'ZAR', 'INR', 'JPY'] as $code)
                    <li>
                        <a class="dropdown-item {{ CurrencyHelper::getUserCurrency() == $code ? 'active' : '' }}" 
                           href="{{ route('settings.currency.update', ['currency_code' => $code]) }}" 
                           onclick="event.preventDefault(); document.getElementById('currency-form-{{ $code }}').submit();">
                            {{ CurrencyHelper::getSymbol($code) }} {{ $code }} - {{ CurrencyHelper::getName($code) }}
                        </a>
                        <form id="currency-form-{{ $code }}" action="{{ route('settings.currency.update') }}" method="POST" style="display: none;">
                            @csrf
                            <input type="hidden" name="currency_code" value="{{ $code }}">
                        </form>
                    </li>
                @endforeach
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="{{ route('settings.index') }}"><i class="fas fa-cog me-2"></i>Currency Settings</a></li>
            </ul>
        </div>
    </div>
</div>

<!-- Filter Form -->

<form method="GET" action="{{ route('finances.index') }}" class="mb-4">
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-12 col-md-3">
                    <label for="year" class="form-label">Year</label>
                    <select class="form-select" id="year" name="year">
                        @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    <label for="month" class="form-label">Month (Optional)</label>
                    <select class="form-select" id="month" name="month">
                        <option value="">All Months</option>
                        @foreach(['01' => 'January', '02' => 'February', '03' => 'March', '04' => 'April', '05' => 'May', '06' => 'June', '07' => 'July', '08' => 'August', '09' => 'September', '10' => 'October', '11' => 'November', '12' => 'December'] as $num => $name)
                            <option value="{{ $num }}" {{ $month == $num ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter me-1"></i>Filter
                    </button>
                </div>
                <div class="col-6 col-md-2">
                    <a href="{{ route('finances.index') }}" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-redo me-1"></i>Reset
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Summary Cards -->
<div class="row g-4 mb-4">
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-1">Net Position</p>
                        <h4 class="mb-0 fw-bold {{ $netPosition >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ CurrencyHelper::format($netPosition) }}
                        </h4>
                    </div>
                    <div class="bg-{{ $netPosition >= 0 ? 'success' : 'danger' }}-subtle rounded p-2">
                        <i class="fas fa-wallet text-{{ $netPosition >= 0 ? 'success' : 'danger' }}"></i>
                    </div>
                </div>
                <small class="text-muted">Income - Expenses + Savings</small>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-1">Total Income</p>
                        <h4 class="mb-0 fw-bold text-success">{{ CurrencyHelper::format($totalIncome) }}</h4>
                    </div>
                    <div class="bg-success-subtle rounded p-2">
                        <i class="fas fa-arrow-up text-success"></i>
                    </div>
                </div>
                <small class="text-muted">All income for {{ $month ? \Carbon\Carbon::create()->month((int)$month)->format('F') : $year }}</small>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-1">Total Expenses</p>
                        <h4 class="mb-0 fw-bold text-danger">{{ CurrencyHelper::format($totalExpenses) }}</h4>
                    </div>
                    <div class="bg-danger-subtle rounded p-2">
                        <i class="fas fa-arrow-down text-danger"></i>
                    </div>
                </div>
                <small class="text-muted">All expenses for {{ $month ? \Carbon\Carbon::create()->month((int)$month)->format('F') : $year }}</small>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-1">Savings</p>
                        <h4 class="mb-0 fw-bold text-primary">{{ CurrencyHelper::format($totalSavings) }}</h4>
                    </div>
                    <div class="bg-primary-subtle rounded p-2">
                        <i class="fas fa-piggy-bank text-primary"></i>
                    </div>
                </div>
                <small class="text-muted">Total active savings</small>
            </div>
        </div>
    </div>
</div>

<!-- Additional Summary Cards -->
<div class="row g-4 mb-4">
    <div class="col-12 col-sm-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-1">Receivables</p>
                        <h4 class="mb-0 fw-bold text-warning">{{ CurrencyHelper::format($totalReceivables) }}</h4>
                    </div>
                    <div class="bg-warning-subtle rounded p-2">
                        <i class="fas fa-hand-holding-usd text-warning"></i>
                    </div>
                </div>
                <small class="text-muted">Outstanding debts to collect</small>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-1">Payables</p>
                        <h4 class="mb-0 fw-bold text-info">{{ CurrencyHelper::format($totalPayables) }}</h4>
                    </div>
                    <div class="bg-info-subtle rounded p-2">
                        <i class="fas fa-file-invoice-dollar text-info"></i>
                    </div>
                </div>
                <small class="text-muted">Outstanding debts to pay</small>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row g-4 mb-4">
    <div class="col-12 col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Income vs Expenses - {{ $year }}</h5>
            </div>
            <div class="card-body">
                <canvas id="incomeExpenseChart" height="300"></canvas>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Income by Category</h5>
            </div>
            <div class="card-body">
                @if($incomeByCategory->count() > 0)
                    <canvas id="incomeCategoryChart" height="300"></canvas>
                @else
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-chart-pie fa-3x mb-3"></i>
                        <p>No income data for this period</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Expense Categories -->
<div class="row g-4 mb-4">
    <div class="col-12 col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Expenses by Category</h5>
            </div>
            <div class="card-body">
                @if($expenseByCategory->count() > 0)
                    <canvas id="expenseCategoryChart" height="250"></canvas>
                @else
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-chart-pie fa-3x mb-3"></i>
                        <p>No expense data for this period</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0"><i class="fas fa-list me-2"></i>Category Breakdown</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0 ps-3">Category</th>
                                <th class="border-0 text-end">Income</th>
                                <th class="border-0 text-end">Expense</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $incomeCategories = $incomeByCategory->keyBy('name');
                                $expenseCategories = $expenseByCategory->keyBy('name');
                                $allCategories = $incomeCategories->merge($expenseCategories)->keys();
                            @endphp
                            @forelse($allCategories as $categoryName)
                                <tr>
                                    <td class="ps-3">
                                        <div class="d-flex align-items-center">
                                            <span class="badge bg-secondary rounded-pill me-2">{{ $loop->iteration }}</span>
                                            {{ $categoryName }}
                                        </div>
                                    </td>
                                    <td class="text-end text-success">
                                        {{ isset($incomeCategories[$categoryName]) ? CurrencyHelper::format($incomeCategories[$categoryName]['total']) : '-' }}
                                    </td>
                                    <td class="text-end text-danger">
                                        {{ isset($expenseCategories[$categoryName]) ? CurrencyHelper::format($expenseCategories[$categoryName]['total']) : '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted">
                                        No category data available
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Top Clients & Outstanding Invoices -->
<div class="row g-4">
    <div class="col-12 col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-users me-2"></i>Top Clients by Revenue</h5>
                <a href="{{ route('clients.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                @if($topClients->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <tbody>
                                @foreach($topClients as $client)
                                    <tr>
                                        <td class="ps-3 py-3">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-primary-subtle rounded-circle p-2 me-3">
                                                    <i class="fas fa-user text-primary small"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-bold">{{ $client->name }}</div>
                                                    <small class="text-muted">{{ $client->email }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="pe-3 py-3 text-end">
                                            <div class="fw-bold text-success">{{ CurrencyHelper::format($client->total_invoiced ?? 0) }}</div>
                                            <small class="text-muted">Total Invoiced</small>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4 text-muted">
                        <i class="fas fa-users fa-2x mb-2"></i>
                        <p class="mb-0">No client data</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-file-invoice-dollar me-2"></i>Outstanding Invoices</h5>
                <a href="{{ route('invoices.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                @if($outstandingInvoices->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <tbody>
                                @foreach($outstandingInvoices as $invoice)
                                    <tr>
                                        <td class="ps-3 py-3">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-warning-subtle rounded-circle p-2 me-3">
                                                    <i class="fas fa-file-invoice text-warning small"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-bold">{{ $invoice->invoice_number }}</div>
                                                    <small class="text-muted">{{ $invoice->client ? $invoice->client->name : 'No Client' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="pe-3 py-3 text-end">
                                            <div class="fw-bold">{{ CurrencyHelper::format($invoice->total_amount) }}</div>
                                            <small class="text-muted">Due: {{ $invoice->due_date->format('M d, Y') }}</small>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4 text-muted">
                        <i class="fas fa-check-circle fa-2x mb-2"></i>
                        <p class="mb-0">No outstanding invoices</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Income vs Expenses Chart
    const incomeExpenseCtx = document.getElementById('incomeExpenseChart').getContext('2d');
    new Chart(incomeExpenseCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($months) !!},
            datasets: [
                {
                    label: 'Income',
                    data: {!! json_encode($incomeData) !!},
                    backgroundColor: '#10B981',
                    borderRadius: 4,
                },
                {
                    label: 'Expenses',
                    data: {!! json_encode($expenseData) !!},
                    backgroundColor: '#EF4444',
                    borderRadius: 4,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + formatCurrency(context.raw);
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#f3f4f6'
                    },
                    ticks: {
                        callback: function(value) {
                            return formatCurrency(value);
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // Income Category Chart
    @if($incomeByCategory->count() > 0)
    const incomeCategoryCtx = document.getElementById('incomeCategoryChart').getContext('2d');
    new Chart(incomeCategoryCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($incomeByCategory->pluck('name')) !!},
            datasets: [{
                data: {!! json_encode($incomeByCategory->pluck('total')) !!},
                backgroundColor: {!! json_encode($incomeByCategory->pluck('color')) !!},
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                }
            }
        }
    });
    @endif

    // Expense Category Chart
    @if($expenseByCategory->count() > 0)
    const expenseCategoryCtx = document.getElementById('expenseCategoryChart').getContext('2d');
    new Chart(expenseCategoryCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($expenseByCategory->pluck('name')) !!},
            datasets: [{
                data: {!! json_encode($expenseByCategory->pluck('total')) !!},
                backgroundColor: {!! json_encode($expenseByCategory->pluck('color')) !!},
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                }
            }
        }
    });
    @endif

    // Currency formatting function
    function formatCurrency(value) {
        const currency = '{{ CurrencyHelper::getUserCurrency() }}';
        const symbol = '{{ CurrencyHelper::getSymbol() }}';
        return symbol + value.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }
</script>
@endsection
