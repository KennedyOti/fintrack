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
</script>

@yield('scripts')
</body>
</html>
