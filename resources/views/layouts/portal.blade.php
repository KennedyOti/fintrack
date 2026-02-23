<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="FinTrack Portal - Financial Management Dashboard">
    <title>@yield('title', 'FinTrack Dashboard')</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Custom CSS -->
    <link href="{{ asset('assets/css/portal.css') }}" rel="stylesheet">
    
    @yield('styles')
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <nav id="sidebar" class="sidebar">
            <div class="sidebar-header">
                <a href="{{ route('dashboard') }}" class="d-flex align-items-center text-white text-decoration-none">
                    <!--<i class="fas fa-wallet fa-lg me-2"></i>-->
                    <span class="fw-bold">FinTrack</span>
                </a>
            </div>
            <ul class="nav flex-column sidebar-menu">
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="fas fa-tachometer-alt me-2"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('finances.index') }}" class="nav-link {{ request()->routeIs('finances.*') ? 'active' : '' }}">
                        <i class="fas fa-chart-line me-2"></i>
                        <span>Financial Overview</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="collapse" href="#incomeMenu">
                        <i class="fas fa-arrow-up me-2"></i>
                        <span>Income</span>
                        <i class="fas fa-chevron-down ms-auto small"></i>
                    </a>
                    <div class="collapse" id="incomeMenu">
                        <ul class="nav flex-column ms-3">
                            <li class="nav-item">
                                <a href="{{ route('income.index') }}" class="nav-link">All Income</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('income.create') }}" class="nav-link">Add Income</a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="collapse" href="#expenseMenu">
                        <i class="fas fa-arrow-down me-2"></i>
                        <span>Expenses</span>
                        <i class="fas fa-chevron-down ms-auto small"></i>
                    </a>
                    <div class="collapse" id="expenseMenu">
                        <ul class="nav flex-column ms-3">
                            <li class="nav-item">
                                <a href="{{ route('expenses.index') }}" class="nav-link">All Expenses</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('expenses.create') }}" class="nav-link">Add Expense</a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a href="{{ route('clients.index') }}" class="nav-link {{ request()->routeIs('clients.*') ? 'active' : '' }}">
                        <i class="fas fa-users me-2"></i>
                        <span>Clients</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('projects.index') }}" class="nav-link {{ request()->routeIs('projects.*') ? 'active' : '' }}">
                        <i class="fas fa-project-diagram me-2"></i>
                        <span>Projects</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('invoices.index') }}" class="nav-link {{ request()->routeIs('invoices.*') ? 'active' : '' }}">
                        <i class="fas fa-file-invoice-dollar me-2"></i>
                        <span>Invoices</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('quotes.index') }}" class="nav-link {{ request()->routeIs('quotes.*') ? 'active' : '' }}">
                        <i class="fas fa-file-contract me-2"></i>
                        <span>Quotes</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="collapse" href="#debtMenu">
                        <i class="fas fa-hand-holding-usd me-2"></i>
                        <span>Debts</span>
                        <i class="fas fa-chevron-down ms-auto small"></i>
                    </a>
                    <div class="collapse" id="debtMenu">
                        <ul class="nav flex-column ms-3">
                            <li class="nav-item">
                                <a href="{{ route('debts.receivable.index') }}" class="nav-link">Receivables</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('debts.payable.index') }}" class="nav-link">Payables</a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a href="{{ route('savings.index') }}" class="nav-link {{ request()->routeIs('savings.*') ? 'active' : '' }}">
                        <i class="fas fa-piggy-bank me-2"></i>
                        <span>Savings</span>
                    </a>
                </li>
            </ul>
            <div class="sidebar-footer">
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown">
                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="d-none d-sm-inline">
                            <strong>{{ Auth::user()->name }}</strong>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-dark text-small shadow">
                        <li><a class="dropdown-item" href="{{ route('settings.index') }}"><i class="fas fa-cog me-2"></i>Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Top Navigation -->
            <nav class="navbar navbar-expand navbar-light bg-white shadow-sm sticky-top top-nav">
                <div class="container-fluid">
                    <button type="button" id="sidebarToggle" class="btn btn-outline-secondary">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div class="d-flex align-items-center ms-auto">
                        <div class="dropdown">
                            <a href="#" class="position-relative me-3" data-bs-toggle="dropdown">
                                <i class="fas fa-bell fa-lg text-muted"></i>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 10px;">
                                    3
                                </span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end shadow">
                                <div class="px-3 py-2 border-bottom">
                                    <strong>Notifications</strong>
                                </div>
                                <a href="#" class="dropdown-item">Invoice #INV-001 overdue</a>
                                <a href="#" class="dropdown-item">Payment received from Client</a>
                                <a href="#" class="dropdown-item">New message</a>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Content -->
            <div class="container-fluid p-4">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Please fix the following errors:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                
                @yield('content')
            </div>

            <!-- Footer -->
            <footer class="portal-footer mt-auto py-3 bg-light">
                <div class="container-fluid">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">&copy; {{ date('Y') }} FinTrack. All rights reserved.</span>
                        <span class="text-muted">Version 1.0.0</span>
                    </div>
                </div>
            </footer>
        </main>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Dashboard JS -->
    <script src="{{ asset('assets/js/dashboard.js') }}"></script>
    
    @yield('scripts')
</body>
</html>
