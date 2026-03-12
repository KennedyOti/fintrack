<!DOCTYPE html>
<html lang="en" data-theme="{{ auth()->check() && auth()->user()->dark_mode ? 'dark' : 'light' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="FinTrack Portal — Financial Management Dashboard">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'FinTrack')</title>

    {{-- Dark mode: apply theme from localStorage before CSS loads to prevent flash --}}
    <script>
    (function () {
        var saved = localStorage.getItem('ft_dark_mode');
        if (saved === 'dark') {
            document.documentElement.setAttribute('data-theme', 'dark');
        } else if (saved === 'light') {
            document.documentElement.setAttribute('data-theme', 'light');
        }
        // If saved is null, server-rendered data-theme from the attribute above takes precedence
    })();
    </script>

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
    <!-- SortableJS (for dashboard card reordering) -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.3/Sortable.min.js"></script>
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

                <li>
                    @php $sidebarUnread = Auth::user()->notifications()->where('is_read', false)->count(); @endphp
                    <a href="{{ route('notifications.index') }}"
                       class="nav-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
                        <i class="fas fa-bell nav-icon"></i>
                        <span>Notifications</span>
                        @if($sidebarUnread > 0)
                        <span class="badge ms-auto" style="background:var(--ft-rose);font-size:10px;min-width:18px;">
                            {{ $sidebarUnread > 99 ? '99+' : $sidebarUnread }}
                        </span>
                        @endif
                    </a>
                </li>

                <li>
                    <a href="{{ route('export.index') }}"
                       class="nav-link {{ request()->routeIs('export.*') ? 'active' : '' }}">
                        <i class="fas fa-download nav-icon"></i>
                        <span>Export Data</span>
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

                {{-- Recurring --}}
                <li>
                    <a href="{{ route('recurring.index') }}"
                       class="nav-link {{ request()->routeIs('recurring.*') ? 'active' : '' }}">
                        <i class="fas fa-sync-alt nav-icon"></i>
                        <span>Recurring</span>
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
                        <span>Project Management</span>
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

                {{-- ── Administration (Admin only) ── --}}
                @if(Auth::user()->role === 'admin')
                <li class="sb-label" style="margin-top:8px;color:rgba(255,255,255,.35);">Administration</li>

                <li>
                    <a href="{{ route('admin.dashboard') }}"
                       class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                       style="{{ request()->routeIs('admin.*') ? '' : '' }}">
                        <i class="fas fa-shield-halved nav-icon"></i>
                        <span>Admin Panel</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('admin.users.index') }}"
                       class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <i class="fas fa-users-gear nav-icon"></i>
                        <span>User Management</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('admin.activity-logs.index') }}"
                       class="nav-link {{ request()->routeIs('admin.activity-logs.*') ? 'active' : '' }}">
                        <i class="fas fa-list-check nav-icon"></i>
                        <span>Activity Logs</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('admin.settings.index') }}"
                       class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                        <i class="fas fa-sliders nav-icon"></i>
                        <span>System Settings</span>
                    </a>
                </li>

                {{-- Free Docs Analytics --}}
                <li>
                    <a href="{{ route('admin.free-docs.analytics') }}"
                       class="nav-link {{ request()->routeIs('admin.free-docs.analytics') ? 'active' : '' }}">
                        <i class="fas fa-chart-column nav-icon"></i>
                        <span>Free Docs Analytics</span>
                    </a>
                </li>

                {{-- Blog Management --}}
                <li>
                    <a class="nav-link {{ request()->routeIs('admin.blog.*') ? 'active' : '' }}"
                       data-bs-toggle="collapse" href="#blogAdminMenu"
                       aria-expanded="{{ request()->routeIs('admin.blog.*') ? 'true' : 'false' }}">
                        <i class="fas fa-rss nav-icon"></i>
                        <span>Blog</span>
                        <i class="fas fa-chevron-down nav-chevron"></i>
                    </a>
                    <div class="collapse {{ request()->routeIs('admin.blog.*') ? 'show' : '' }}" id="blogAdminMenu">
                        <ul class="sidebar-submenu">
                            <li>
                                <a href="{{ route('admin.blog.index') }}"
                                   class="nav-link {{ request()->routeIs('admin.blog.index') ? 'active' : '' }}">All Posts</a>
                            </li>
                            <li>
                                <a href="{{ route('admin.blog.create') }}"
                                   class="nav-link {{ request()->routeIs('admin.blog.create') ? 'active' : '' }}">New Post</a>
                            </li>
                            <li>
                                <a href="{{ route('admin.blog.categories.index') }}"
                                   class="nav-link {{ request()->routeIs('admin.blog.categories.*') ? 'active' : '' }}">Categories</a>
                            </li>
                            <li>
                                <a href="{{ route('admin.blog.comments.index') }}"
                                   class="nav-link {{ request()->routeIs('admin.blog.comments.*') ? 'active' : '' }}">
                                    Comments
                                    @php $pendingBlogComments = \App\Models\BlogComment::where('status','pending')->count(); @endphp
                                    @if($pendingBlogComments > 0)
                                    <span class="badge ms-auto" style="background:var(--ft-rose);font-size:10px;min-width:18px;">{{ $pendingBlogComments }}</span>
                                    @endif
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                @endif

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
                        <div class="sidebar-user-role">
                            {{ Auth::user()->role === 'admin' ? 'Administrator' : 'Freelancer' }}
                        </div>
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

                {{-- ── Quick-Add Button ── --}}
                <button class="topnav-btn quick-add-btn" id="quickAddBtn"
                        data-bs-toggle="modal" data-bs-target="#quickAddModal"
                        aria-label="Quick add transaction" title="Quick add income or expense">
                    <i class="fas fa-circle-plus"></i>
                </button>

                {{-- ── Dark Mode Toggle ── --}}
                <button class="topnav-btn" id="darkModeToggle"
                        aria-label="Toggle dark mode" title="Toggle dark mode">
                    <i class="fas fa-moon" id="darkModeIcon"></i>
                </button>

                {{-- Notification Bell --}}
                @php
                    $navNotifs     = Auth::user()->notifications()->where('is_read', false)->latest()->take(6)->get();
                    $navUnreadCount = Auth::user()->notifications()->where('is_read', false)->count();
                @endphp
                <div class="dropdown">
                    <button class="topnav-btn position-relative" id="notifBellBtn"
                            data-bs-toggle="dropdown" aria-label="Notifications"
                            data-bs-auto-close="outside">
                        <i class="fas fa-bell"></i>
                        @if($navUnreadCount > 0)
                        <span class="notif-badge">{{ $navUnreadCount > 99 ? '99+' : $navUnreadCount }}</span>
                        @endif
                    </button>
                    <div class="dropdown-menu dropdown-menu-end notif-dropdown p-0" style="width:320px;">
                        {{-- Header --}}
                        <div class="notif-dd-header">
                            <span>Notifications</span>
                            <div class="d-flex align-items-center gap-2">
                                @if($navUnreadCount > 0)
                                <button class="btn btn-xs notif-mark-all-btn" id="markAllReadBtn"
                                        title="Mark all as read">
                                    <i class="fas fa-check-double me-1"></i>Mark all read
                                </button>
                                @endif
                                <a href="{{ route('notifications.index') }}"
                                   class="btn btn-xs notif-settings-btn" title="All notifications">
                                    <i class="fas fa-arrow-up-right-from-square"></i>
                                </a>
                            </div>
                        </div>
                        {{-- List --}}
                        <div class="notif-dd-list" id="notifList">
                            @forelse($navNotifs as $notif)
                            <a href="{{ $notif->getLink() }}"
                               class="notif-dd-item {{ $notif->is_read ? '' : 'unread' }}"
                               data-id="{{ $notif->id }}"
                               data-read="{{ $notif->is_read ? 'true' : 'false' }}">
                                <div class="notif-dd-icon" style="background:{{ $notif->getIconBg() }};">
                                    <i class="{{ $notif->getIcon() }}" style="color:{{ $notif->getIconColor() }};"></i>
                                </div>
                                <div class="notif-dd-body">
                                    <div class="notif-dd-title">{{ $notif->title }}</div>
                                    <div class="notif-dd-msg">{{ Str::limit($notif->message, 72) }}</div>
                                    <div class="notif-dd-time">{{ $notif->created_at->diffForHumans() }}</div>
                                </div>
                                <button class="notif-dismiss-btn" title="Dismiss"
                                        data-dismiss-id="{{ $notif->id }}"
                                        onclick="event.preventDefault(); dismissNotif(this);">
                                    <i class="fas fa-xmark"></i>
                                </button>
                            </a>
                            @empty
                            <div class="notif-empty">
                                <i class="fas fa-bell-slash"></i>
                                <p>You're all caught up!</p>
                                <span>No new notifications</span>
                            </div>
                            @endforelse
                        </div>
                        {{-- Footer --}}
                        <div class="notif-dd-footer">
                            <a href="{{ route('notifications.index') }}" class="notif-view-all">
                                View all notifications
                                @if($navUnreadCount > 6)
                                <span class="badge rounded-pill ms-1" style="background:var(--ft-teal);font-size:10px;">
                                    +{{ $navUnreadCount - 6 }} more
                                </span>
                                @endif
                            </a>
                        </div>
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

<!-- ════════════════ QUICK-ADD MODAL ════════════════ -->
@php
    $qaIncomeCategories  = App\Models\Category::where('user_id', Auth::id())->where('type', 'income')->orderBy('name')->get();
    $qaExpenseCategories = App\Models\Category::where('user_id', Auth::id())->where('type', 'expense')->orderBy('name')->get();
@endphp
<div class="modal fade" id="quickAddModal" tabindex="-1" aria-labelledby="quickAddModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:440px;">
        <div class="modal-content">
            <div class="modal-header" style="padding:14px 18px;">
                <div class="d-flex align-items-center gap-3">
                    <div class="qa-modal-icon">
                        <i class="fas fa-circle-plus"></i>
                    </div>
                    <div>
                        <h5 class="modal-title mb-0" id="quickAddModalLabel">Quick Add</h5>
                        <div style="font-size:12px;color:var(--text-muted);">Log a transaction without leaving this page</div>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            {{-- Tab Switcher --}}
            <div class="qa-tabs">
                <button class="qa-tab active" data-qa-tab="income" type="button">
                    <i class="fas fa-arrow-trend-up"></i> Income
                </button>
                <button class="qa-tab" data-qa-tab="expense" type="button">
                    <i class="fas fa-arrow-trend-down"></i> Expense
                </button>
            </div>

            <div class="modal-body">

                {{-- ── INCOME FORM ── --}}
                <form id="qaIncomeForm" class="qa-form" data-qa-type="income">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Amount <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text" style="font-size:13px;font-weight:700;color:var(--ft-emerald);">
                                    <i class="fas fa-arrow-up"></i>
                                </span>
                                <input type="number" class="form-control" name="amount" placeholder="0.00"
                                       min="0.01" step="0.01" required>
                            </div>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="income_date"
                                   value="{{ now()->format('Y-m-d') }}" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Method <span class="text-danger">*</span></label>
                            <select class="form-select" name="payment_method" required>
                                <option value="cash">Cash</option>
                                <option value="bank_transfer" selected>Bank Transfer</option>
                                <option value="mpesa">M-Pesa</option>
                                <option value="card">Card</option>
                                <option value="paypal">PayPal</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Category</label>
                            <select class="form-select" name="category_id">
                                <option value="">— None —</option>
                                @foreach($qaIncomeCategories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Notes</label>
                            <input type="text" class="form-control" name="notes" placeholder="Optional note…" maxlength="500">
                        </div>
                    </div>
                </form>

                {{-- ── EXPENSE FORM ── --}}
                <form id="qaExpenseForm" class="qa-form d-none" data-qa-type="expense">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Amount <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text" style="font-size:13px;font-weight:700;color:var(--ft-rose);">
                                    <i class="fas fa-arrow-down"></i>
                                </span>
                                <input type="number" class="form-control" name="amount" placeholder="0.00"
                                       min="0.01" step="0.01" required>
                            </div>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="expense_date"
                                   value="{{ now()->format('Y-m-d') }}" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Method <span class="text-danger">*</span></label>
                            <select class="form-select" name="payment_method" required>
                                <option value="cash">Cash</option>
                                <option value="bank_transfer" selected>Bank Transfer</option>
                                <option value="mpesa">M-Pesa</option>
                                <option value="card">Card</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Category</label>
                            <select class="form-select" name="category_id">
                                <option value="">— None —</option>
                                @foreach($qaExpenseCategories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Vendor / Payee</label>
                            <input type="text" class="form-control" name="vendor_name" placeholder="Who did you pay?…" maxlength="150">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Notes</label>
                            <input type="text" class="form-control" name="notes" placeholder="Optional note…" maxlength="500">
                        </div>
                    </div>
                </form>

                {{-- Inline feedback --}}
                <div id="qaFeedback" class="mt-3" style="display:none;"></div>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary btn-sm" id="qaSubmitBtn">
                    <i class="fas fa-plus me-1"></i>
                    <span id="qaSubmitLabel">Add Income</span>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ── Quick-Add Toast Notification ── --}}
<div class="qa-toast-container" id="qaToastContainer"></div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- Portal JS -->
<script src="{{ asset('assets/js/dashboard.js') }}"></script>

<script>
// ─── Notification Bell Interactions ──────────────────────────────────────────
(function () {
    const MARK_ALL_URL  = '{{ route('notifications.markAllRead') }}';
    const CSRF          = '{{ csrf_token() }}';

    // Mark item as read when clicked (before navigating)
    document.querySelectorAll('.notif-dd-item[data-id]').forEach(function (el) {
        el.addEventListener('click', function () {
            if (el.dataset.read === 'false') {
                fetch('/notifications/' + el.dataset.id + '/read', {
                    method:  'PATCH',
                    headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
                }).catch(function () {});
                el.classList.remove('unread');
                el.dataset.read = 'true';
                refreshBadge(-1);
            }
        });
    });

    // Mark all read
    var markAllBtn = document.getElementById('markAllReadBtn');
    if (markAllBtn) {
        markAllBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            fetch(MARK_ALL_URL, {
                method:  'POST',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
            }).then(function () {
                document.querySelectorAll('.notif-dd-item.unread').forEach(function (el) {
                    el.classList.remove('unread');
                    el.dataset.read = 'true';
                });
                refreshBadge(0, true);
                markAllBtn.closest('.d-flex') && markAllBtn.remove();
            }).catch(function () {});
        });
    }

    // Dismiss (delete) a notification
    window.dismissNotif = function (btn) {
        var id = btn.dataset.dismissId;
        fetch('/notifications/' + id, {
            method:  'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
        }).then(function () {
            var item = btn.closest('.notif-dd-item');
            if (item) {
                if (!item.classList.contains('unread') === false) refreshBadge(-1);
                item.style.transition = 'opacity .2s';
                item.style.opacity    = '0';
                setTimeout(function () { item.remove(); }, 220);
            }
        }).catch(function () {});
    };

    // Update badge counter
    function refreshBadge(delta, clear) {
        var badge = document.querySelector('.notif-badge');
        if (clear) {
            if (badge) badge.remove();
            return;
        }
        if (!badge) return;
        var current = parseInt(badge.textContent) || 0;
        var next    = Math.max(0, current + delta);
        if (next === 0) {
            badge.remove();
        } else {
            badge.textContent = next > 99 ? '99+' : next;
        }
    }
})();

// ─── Quick-Add Modal ──────────────────────────────────────────────────────────
(function () {
    var CSRF          = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    var INCOME_URL    = '{{ route('quick-add.income') }}';
    var EXPENSE_URL   = '{{ route('quick-add.expense') }}';

    var currentTab    = 'income';
    var tabs          = document.querySelectorAll('.qa-tab');
    var forms         = { income: document.getElementById('qaIncomeForm'), expense: document.getElementById('qaExpenseForm') };
    var submitBtn     = document.getElementById('qaSubmitBtn');
    var submitLabel   = document.getElementById('qaSubmitLabel');
    var feedbackEl    = document.getElementById('qaFeedback');

    function switchTab(tab) {
        currentTab = tab;
        tabs.forEach(function (t) {
            t.classList.toggle('active', t.dataset.qaTab === tab);
        });
        forms.income.classList.toggle('d-none', tab !== 'income');
        forms.expense.classList.toggle('d-none', tab !== 'expense');
        submitLabel.textContent = tab === 'income' ? 'Add Income' : 'Add Expense';
        feedbackEl.style.display = 'none';
    }

    tabs.forEach(function (btn) {
        btn.addEventListener('click', function () { switchTab(btn.dataset.qaTab); });
    });

    // Reset modal when hidden
    document.getElementById('quickAddModal').addEventListener('hidden.bs.modal', function () {
        forms.income.reset();
        forms.expense.reset();
        // Re-set today's date
        var today = new Date().toISOString().split('T')[0];
        var inDateEl = forms.income.querySelector('[name="income_date"]');
        var exDateEl = forms.expense.querySelector('[name="expense_date"]');
        if (inDateEl) inDateEl.value = today;
        if (exDateEl) exDateEl.value = today;
        feedbackEl.style.display = 'none';
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-plus me-1"></i><span id="qaSubmitLabel">Add Income</span>';
        submitLabel = document.getElementById('qaSubmitLabel');
        switchTab('income');
    });

    // Submit
    submitBtn.addEventListener('click', function () {
        var form    = forms[currentTab];
        var url     = currentTab === 'income' ? INCOME_URL : EXPENSE_URL;
        var data    = new FormData(form);

        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Saving…';

        fetch(url, {
            method:  'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body:    data,
        })
        .then(function (res) { return res.json(); })
        .then(function (json) {
            if (json.success) {
                showToast(json.message, 'success');
                bootstrap.Modal.getInstance(document.getElementById('quickAddModal')).hide();
            } else {
                var msgs = json.errors ? Object.values(json.errors).flat().join('<br>') : (json.message || 'Failed to save.');
                feedbackEl.innerHTML = '<div class="alert alert-danger mb-0" style="font-size:12.5px;">' + msgs + '</div>';
                feedbackEl.style.display = 'block';
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-plus me-1"></i><span id="qaSubmitLabel">' + (currentTab === 'income' ? 'Add Income' : 'Add Expense') + '</span>';
                submitLabel = document.getElementById('qaSubmitLabel');
            }
        })
        .catch(function () {
            feedbackEl.innerHTML = '<div class="alert alert-danger mb-0" style="font-size:12.5px;">Network error. Please try again.</div>';
            feedbackEl.style.display = 'block';
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-plus me-1"></i><span id="qaSubmitLabel">' + (currentTab === 'income' ? 'Add Income' : 'Add Expense') + '</span>';
            submitLabel = document.getElementById('qaSubmitLabel');
        });
    });

    // Toast helper
    window.showToast = function (msg, type) {
        var container = document.getElementById('qaToastContainer');
        var color = type === 'success' ? 'var(--ft-emerald)' : 'var(--ft-rose)';
        var icon  = type === 'success' ? 'fas fa-circle-check' : 'fas fa-circle-xmark';
        var el    = document.createElement('div');
        el.className = 'qa-toast';
        el.innerHTML = '<i class="' + icon + '" style="color:' + color + ';font-size:15px;flex-shrink:0;"></i><span>' + msg + '</span>';
        container.appendChild(el);
        requestAnimationFrame(function () { el.classList.add('show'); });
        setTimeout(function () {
            el.classList.remove('show');
            setTimeout(function () { el.remove(); }, 350);
        }, 3500);
    };
})();
</script>

@yield('scripts')
@stack('scripts')
</body>
</html>
