<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="FinTrack Portal — Financial Management Dashboard">
    <title>@yield('title', 'FinTrack')</title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Portal CSS -->
    <link href="{{ asset('assets/css/portal.css') }}" rel="stylesheet">

    @yield('styles')
</head>
<body>

<!-- Mobile sidebar overlay -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<div class="wrapper">

    <!-- ════════════════ SIDEBAR ════════════════ -->
    <nav id="sidebar" class="sidebar">

        {{-- Brand --}}
        <a href="{{ route('dashboard') }}" class="sidebar-brand">
            <div class="sidebar-brand-icon">
                <i class="fas fa-wallet"></i>
            </div>
            <span class="sidebar-brand-name">Fin<span>Track</span></span>
        </a>

        {{-- Nav --}}
        <div class="sidebar-nav">
            <ul class="sidebar-menu">

                {{-- ── Main ── --}}
                <li class="sb-label">Main</li>

                <li>
                    <a href="{{ route('dashboard') }}"
                       class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="fas fa-gauge-high nav-icon"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('finances.index') }}"
                       class="nav-link {{ request()->routeIs('finances.*') ? 'active' : '' }}">
                        <i class="fas fa-chart-line nav-icon"></i>
                        <span>Financial Overview</span>
                    </a>
                </li>

                {{-- ── Money ── --}}
                <li class="sb-label">Money</li>

                {{-- Income --}}
                <li>
                    <a class="nav-link {{ request()->routeIs('income.*') ? 'active' : '' }}"
                       data-bs-toggle="collapse" href="#incomeMenu"
                       aria-expanded="{{ request()->routeIs('income.*') ? 'true' : 'false' }}">
                        <i class="fas fa-arrow-trend-up nav-icon"></i>
                        <span>Income</span>
                        <i class="fas fa-chevron-down nav-chevron"></i>
                    </a>
                    <div class="collapse {{ request()->routeIs('income.*') ? 'show' : '' }}" id="incomeMenu">
                        <ul class="sidebar-submenu">
                            <li>
                                <a href="{{ route('income.index') }}"
                                   class="nav-link {{ request()->routeIs('income.index') ? 'active' : '' }}">All Income</a>
                            </li>
                            <li>
                                <a href="{{ route('income.create') }}"
                                   class="nav-link {{ request()->routeIs('income.create') ? 'active' : '' }}">Add Income</a>
                            </li>
                        </ul>
                    </div>
                </li>

                {{-- Expenses --}}
                <li>
                    <a class="nav-link {{ request()->routeIs('expenses.*') ? 'active' : '' }}"
                       data-bs-toggle="collapse" href="#expenseMenu"
                       aria-expanded="{{ request()->routeIs('expenses.*') ? 'true' : 'false' }}">
                        <i class="fas fa-arrow-trend-down nav-icon"></i>
                        <span>Expenses</span>
                        <i class="fas fa-chevron-down nav-chevron"></i>
                    </a>
                    <div class="collapse {{ request()->routeIs('expenses.*') ? 'show' : '' }}" id="expenseMenu">
                        <ul class="sidebar-submenu">
                            <li>
                                <a href="{{ route('expenses.index') }}"
                                   class="nav-link {{ request()->routeIs('expenses.index') ? 'active' : '' }}">All Expenses</a>
                            </li>
                            <li>
                                <a href="{{ route('expenses.create') }}"
                                   class="nav-link {{ request()->routeIs('expenses.create') ? 'active' : '' }}">Add Expense</a>
                            </li>
                        </ul>
                    </div>
                </li>

                {{-- Savings --}}
                <li>
                    <a href="{{ route('savings.index') }}"
                       class="nav-link {{ request()->routeIs('savings.*') ? 'active' : '' }}">
                        <i class="fas fa-piggy-bank nav-icon"></i>
                        <span>Savings</span>
                    </a>
                </li>

                {{-- ── Business ── --}}
                <li class="sb-label">Business</li>

                {{-- Clients --}}
                <li>
                    <a href="{{ route('clients.index') }}"
                       class="nav-link {{ request()->routeIs('clients.*') ? 'active' : '' }}">
                        <i class="fas fa-users nav-icon"></i>
                        <span>Clients</span>
                    </a>
                </li>

                {{-- Projects --}}
                <li>
                    <a href="{{ route('projects.index') }}"
                       class="nav-link {{ request()->routeIs('projects.*') ? 'active' : '' }}">
                        <i class="fas fa-diagram-project nav-icon"></i>
                        <span>Projects</span>
                    </a>
                </li>

                {{-- Invoices --}}
                <li>
                    <a href="{{ route('invoices.index') }}"
                       class="nav-link {{ request()->routeIs('invoices.*') ? 'active' : '' }}">
                        <i class="fas fa-file-invoice-dollar nav-icon"></i>
                        <span>Invoices</span>
                    </a>
                </li>

                {{-- Quotes --}}
                <li>
                    <a href="{{ route('quotes.index') }}"
                       class="nav-link {{ request()->routeIs('quotes.*') ? 'active' : '' }}">
                        <i class="fas fa-file-contract nav-icon"></i>
                        <span>Quotes</span>
                    </a>
                </li>

                {{-- Debts --}}
                <li>
                    <a class="nav-link {{ request()->routeIs('debts.*') ? 'active' : '' }}"
                       data-bs-toggle="collapse" href="#debtMenu"
                       aria-expanded="{{ request()->routeIs('debts.*') ? 'true' : 'false' }}">
                        <i class="fas fa-handshake nav-icon"></i>
                        <span>Debts</span>
                        <i class="fas fa-chevron-down nav-chevron"></i>
                    </a>
                    <div class="collapse {{ request()->routeIs('debts.*') ? 'show' : '' }}" id="debtMenu">
                        <ul class="sidebar-submenu">
                            <li>
                                <a href="{{ route('debts.receivable.index') }}"
                                   class="nav-link {{ request()->routeIs('debts.receivable.*') ? 'active' : '' }}">Receivables</a>
                            </li>
                            <li>
                                <a href="{{ route('debts.payable.index') }}"
                                   class="nav-link {{ request()->routeIs('debts.payable.*') ? 'active' : '' }}">Payables</a>
                            </li>
                        </ul>
                    </div>
                </li>

            </ul>
        </div>

        {{-- Sidebar Footer / User --}}
        <div class="sidebar-footer">
            <div class="dropdown dropup w-100">
                <button class="sidebar-user dropdown-toggle" id="sidebarUserMenu"
                        data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="sidebar-user-avatar">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div class="flex-1" style="min-width:0;flex:1;">
                        <div class="sidebar-user-name">{{ Auth::user()->name }}</div>
                        <div class="sidebar-user-role">Freelancer</div>
                    </div>
                    <i class="fas fa-ellipsis-vertical" style="color:rgba(255,255,255,.35);font-size:11px;flex-shrink:0;"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-dark w-100" style="margin-bottom:4px;">
                    <li>
                        <a class="dropdown-item" href="{{ route('settings.index') }}">
                            <i class="fas fa-gear"></i> Settings
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger w-100 text-start">
                                <i class="fas fa-right-from-bracket"></i> Sign out
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>

    </nav>
    {{-- /sidebar --}}

    <!-- ════════════════ MAIN CONTENT ════════════════ -->
    <div class="main-content">

        {{-- Top Navigation --}}
        <header class="topnav">
            <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle sidebar">
                <i class="fas fa-bars"></i>
            </button>

            {{-- Logo shown on mobile only --}}
            <a href="{{ route('dashboard') }}" class="topnav-brand">
                <i class="fas fa-wallet" style="color:var(--ft-teal);font-size:15px;"></i>
                Fin<span>Track</span>
            </a>

            <div class="topnav-spacer"></div>

            <div class="topnav-actions">

                {{-- Notification Bell --}}
                <div class="dropdown">
                    <button class="topnav-btn" data-bs-toggle="dropdown" aria-label="Notifications">
                        <i class="fas fa-bell"></i>
                        <span class="topnav-dot"></span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end" style="width:270px;">
                        <div class="dropdown-header">Notifications</div>
                        <a href="{{ route('invoices.index') }}" class="dropdown-item">
                            <i class="fas fa-file-invoice" style="color:var(--ft-amber);"></i>
                            <span>Invoice #INV-001 is overdue</span>
                        </a>
                        <a href="{{ route('invoices.index') }}" class="dropdown-item">
                            <i class="fas fa-circle-check" style="color:#059669;"></i>
                            <span>Payment received from client</span>
                        </a>
                        <hr class="dropdown-divider">
                        <a href="#" class="dropdown-item justify-content-center"
                           style="font-size:12px;color:var(--ft-teal);">
                            View all notifications
                        </a>
                    </div>
                </div>

                {{-- Settings quick-link --}}
                <a href="{{ route('settings.index') }}" class="topnav-btn" aria-label="Settings">
                    <i class="fas fa-gear"></i>
                </a>

                {{-- User dropdown (desktop) --}}
                <div class="dropdown d-none d-lg-block">
                    <button class="topnav-user dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="topnav-user-avatar">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <span class="topnav-user-name">{{ Auth::user()->name }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="{{ route('settings.index') }}">
                                <i class="fas fa-user"></i> Profile & Settings
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger w-100 text-start">
                                    <i class="fas fa-right-from-bracket"></i> Sign out
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>

            </div>
        </header>

        {{-- Flash Messages --}}
        @if(session('success') || session('error') || $errors->any())
        <div style="padding: 12px 20px 0;">
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-circle-check" style="flex-shrink:0;"></i>
                <span>{{ session('success') }}</span>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-circle-xmark" style="flex-shrink:0;"></i>
                <span>{{ session('error') }}</span>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-triangle-exclamation" style="flex-shrink:0;margin-top:2px;"></i>
                <div>
                    <strong>Please fix the following errors:</strong>
                    <ul class="mb-0 mt-1 ps-3" style="font-size:12.5px;">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif
        </div>
        @endif

        {{-- Page Content --}}
        <div class="page-content fade-in">
            @yield('content')
        </div>

        {{-- Footer --}}
        <footer class="portal-footer">
            <span>&copy; {{ date('Y') }} FinTrack. All rights reserved.</span>
            <span>v1.0.0</span>
        </footer>

    </div>
    {{-- /main-content --}}

</div>
{{-- /wrapper --}}

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- Portal JS -->
<script src="{{ asset('assets/js/dashboard.js') }}"></script>

@yield('scripts')
</body>
</html>
