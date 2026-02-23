@extends('layouts.portal')

@section('title', 'Dashboard - FinTrack')

@section('content')
<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">Dashboard</h4>
        <p class="text-muted mb-0">Welcome back, {{ Auth::user()->name }}!</p>
    </div>
    <div>
        <span class="text-muted">{{ now()->format('l, F j, Y') }}</span>
    </div>
    <div class="d-flex align-items-center gap-2">
        <!-- Currency Selector -->
        <div class="dropdown">
            <button class="btn btn-outline-secondary dropdown-toggle btn-sm" type="button" data-bs-toggle="dropdown">
                <i class="fas fa-coins me-1"></i>
                {{ $currencySymbol }} {{ $currencyCode }}
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                @foreach(['USD', 'EUR', 'GBP', 'KES', 'NGN', 'ZAR', 'INR', 'JPY'] as $code)
                    <li>
                        <a class="dropdown-item {{ $currencyCode == $code ? 'active' : '' }}" 
                           href="{{ route('settings.currency.update', ['currency_code' => $code]) }}" 
                           onclick="event.preventDefault(); document.getElementById('currency-form-{{ $code }}').submit();">
                            @if($code == 'USD')$@elseif($code == 'EUR')€@elseif($code == 'GBP')£@elseif($code == 'KES')KSh@elseif($code == 'NGN')₦@elseif($code == 'ZAR')R@elseif($code == 'INR')₹@elseif($code == 'JPY')¥@else{{ $code }}@endif {{ $code }}
                        </a>
                        <form id="currency-form-{{ $code }}" action="{{ route('settings.currency.update') }}" method="POST" style="display: none;">
                            @csrf
                            <input type="hidden" name="currency_code" value="{{ $code }}">
                        </form>
                    </li>
                @endforeach
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="{{ route('settings.index') }}"><i class="fas fa-cog me-2"></i>More Settings</a></li>
            </ul>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="row g-4 mb-4">
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-1">Net Position</p>
                        <h4 class="mb-0 fw-bold {{ $netPosition >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ $currencySymbol }}{{ number_format($netPosition, 2) }}
                        </h4>
                    </div>
                    <div class="bg-success-subtle rounded p-2">
                        <i class="fas fa-wallet text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-1">Total Income</p>
                        <h4 class="mb-0 fw-bold text-success">{{ $currencySymbol }}{{ number_format($totalIncome, 2) }}</h4>
                    </div>
                    <div class="bg-success-subtle rounded p-2">
                        <i class="fas fa-arrow-up text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-1">Total Expenses</p>
                        <h4 class="mb-0 fw-bold text-danger">{{ $currencySymbol }}{{ number_format($totalExpenses, 2) }}</h4>
                    </div>
                    <div class="bg-danger-subtle rounded p-2">
                        <i class="fas fa-arrow-down text-danger"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-1">Savings</p>
                        <h4 class="mb-0 fw-bold text-primary">{{ $currencySymbol }}{{ number_format($totalSavings, 2) }}</h4>
                    </div>
                    <div class="bg-primary-subtle rounded p-2">
                        <i class="fas fa-piggy-bank text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row g-4 mb-4">
    <div class="col-12 col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Income vs Expenses</h5>
            </div>
            <div class="card-body">
                <canvas id="incomeExpenseChart" height="300"></canvas>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Expenses by Category</h5>
            </div>
            <div class="card-body">
                <canvas id="expenseCategoryChart" height="300"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Quick Stats -->
<div class="row g-4 mb-4">
    <div class="col-12 col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="bg-warning-subtle rounded p-3 me-3">
                        <i class="fas fa-briefcase text-warning fa-lg"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-0">Active Projects</p>
                        <h4 class="mb-0 fw-bold">{{ $activeProjects }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="bg-info-subtle rounded p-3 me-3">
                        <i class="fas fa-users text-info fa-lg"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-0">Total Clients</p>
                        <h4 class="mb-0 fw-bold">{{ $totalClients }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="bg-danger-subtle rounded p-3 me-3">
                        <i class="fas fa-file-invoice-dollar text-danger fa-lg"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-0">Pending Invoices</p>
                        <h4 class="mb-0 fw-bold">{{ $pendingInvoices }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Transactions -->
<div class="row g-4">
    <div class="col-12 col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-arrow-up me-2 text-success"></i>Recent Income</h5>
                <a href="{{ route('income.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                @if($recentIncomes->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <tbody>
                            @foreach($recentIncomes as $income)
                            <tr>
                                <td class="ps-3 py-3">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-success-subtle rounded-circle p-2 me-3">
                                            <i class="fas fa-arrow-up text-success small"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold">{{ $income->category ? $income->category->name : 'Uncategorized' }}</div>
                                            <small class="text-muted">{{ $income->client ? $income->client->name : '-' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="pe-3 py-3 text-end">
                                    <div class="fw-bold text-success">+{{ $currencySymbol }}{{ number_format($income->amount, 2) }}</div>
                                    <small class="text-muted">{{ $income->income_date->format('M d') }}</small>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4 text-muted">
                    <i class="fas fa-inbox fa-2x mb-2"></i>
                    <p class="mb-0">No recent income</p>
                </div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-arrow-down me-2 text-danger"></i>Recent Expenses</h5>
                <a href="{{ route('expenses.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                @if($recentExpenses->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <tbody>
                            @foreach($recentExpenses as $expense)
                            <tr>
                                <td class="ps-3 py-3">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-danger-subtle rounded-circle p-2 me-3">
                                            <i class="fas fa-arrow-down text-danger small"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold">{{ $expense->category ? $expense->category->name : 'Uncategorized' }}</div>
                                            <small class="text-muted">{{ $expense->vendor_name ?: '-' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="pe-3 py-3 text-end">
                                    <div class="fw-bold text-danger">-{{ $currencySymbol }}{{ number_format($expense->amount, 2) }}</div>
                                    <small class="text-muted">{{ $expense->expense_date->format('M d') }}</small>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4 text-muted">
                    <i class="fas fa-inbox fa-2x mb-2"></i>
                    <p class="mb-0">No recent expenses</p>
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
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#f3f4f6'
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

    // Expense Category Chart
    const expenseCategoryCtx = document.getElementById('expenseCategoryChart').getContext('2d');
    const categoryLabels = [];
    const categoryData = [];
    const categoryColors = [];
    
    @foreach($expensesByCategory as $categoryId => $categoryData)
        categoryLabels.push('{{ $categoryData['name'] }}');
        categoryData.push({{ $categoryData['total'] }});
        categoryColors.push('#' + Math.floor(Math.random()*16777215).toString(16));
    @endforeach

    new Chart(expenseCategoryCtx, {
        type: 'doughnut',
        data: {
            labels: categoryLabels.length > 0 ? categoryLabels : ['No Data'],
            datasets: [{
                data: categoryData.length > 0 ? categoryData : [1],
                backgroundColor: categoryColors.length > 0 ? categoryColors : ['#e9ecef'],
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
</script>
@endsection
