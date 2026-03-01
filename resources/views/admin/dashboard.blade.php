@extends('layouts.portal')

@section('title', 'Admin Dashboard — FinTrack')

@section('content')

{{-- ── Page Header ─────────────────────────────────────────────────────────── --}}
<div class="page-header">
    <div>
        <h1 class="page-title">
            <i class="fas fa-shield-halved me-2" style="color:var(--ft-teal);font-size:20px;"></i>
            Admin Dashboard
        </h1>
        <p class="page-subtitle">Platform overview · {{ now()->format('l, F j, Y') }}</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-users-gear me-1"></i> Manage Users
        </a>
        <a href="{{ route('admin.activity-logs.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-list-check me-1"></i> Activity Logs
        </a>
        <a href="{{ route('admin.settings.index') }}" class="btn btn-sm" style="background:var(--ft-navy);color:#fff;">
            <i class="fas fa-gear me-1"></i> Settings
        </a>
    </div>
</div>

{{-- ── User Stats Row ───────────────────────────────────────────────────────── --}}
<div class="row g-3 mb-4">

    <div class="col-6 col-xl-3">
        <div class="card stat-card sc-navy">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label">Total Users</div>
                    <div class="stat-value">{{ number_format($totalUsers) }}</div>
                    <div class="stat-sub">
                        <i class="fas fa-user-slash me-1"></i>{{ $deletedUsers }} deleted
                    </div>
                </div>
                <div class="stat-badge"><i class="fas fa-users"></i></div>
            </div>
        </div>
    </div>

    <div class="col-6 col-xl-3">
        <div class="card stat-card sc-emerald">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label">Active Users</div>
                    <div class="stat-value">{{ number_format($activeUsers) }}</div>
                    <div class="stat-sub">
                        <i class="fas fa-user-shield me-1"></i>{{ $totalAdmins }} admin(s)
                    </div>
                </div>
                <div class="stat-badge"><i class="fas fa-circle-check"></i></div>
            </div>
        </div>
    </div>

    <div class="col-6 col-xl-3">
        <div class="card stat-card sc-rose">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label">Suspended</div>
                    <div class="stat-value">{{ number_format($suspendedUsers) }}</div>
                    <div class="stat-sub">
                        <i class="fas fa-ban me-1"></i>Blocked accounts
                    </div>
                </div>
                <div class="stat-badge"><i class="fas fa-ban"></i></div>
            </div>
        </div>
    </div>

    <div class="col-6 col-xl-3">
        <div class="card stat-card sc-teal">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label">New This Month</div>
                    <div class="stat-value">{{ number_format($newUsersThisMonth) }}</div>
                    <div class="stat-sub">
                        <i class="fas fa-calendar-plus me-1"></i>{{ now()->format('F Y') }}
                    </div>
                </div>
                <div class="stat-badge"><i class="fas fa-user-plus"></i></div>
            </div>
        </div>
    </div>

</div>

{{-- ── Platform Financial Stats ─────────────────────────────────────────────── --}}
<div class="row g-3 mb-4">

    <div class="col-6 col-xl-3">
        <div class="card">
            <div class="card-body" style="padding:13px 16px!important;">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:40px;height:40px;background:rgba(5,150,105,.1);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="fas fa-arrow-trend-up" style="color:#059669;font-size:15px;"></i>
                    </div>
                    <div>
                        <div style="font-size:10.5px;color:var(--text-muted);font-weight:700;text-transform:uppercase;letter-spacing:.4px;">Platform Income</div>
                        <div style="font-size:20px;font-weight:800;color:var(--text-h);">${{ number_format($platformIncome, 0) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-xl-3">
        <div class="card">
            <div class="card-body" style="padding:13px 16px!important;">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:40px;height:40px;background:rgba(244,63,94,.08);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="fas fa-arrow-trend-down" style="color:var(--ft-rose);font-size:15px;"></i>
                    </div>
                    <div>
                        <div style="font-size:10.5px;color:var(--text-muted);font-weight:700;text-transform:uppercase;letter-spacing:.4px;">Platform Expenses</div>
                        <div style="font-size:20px;font-weight:800;color:var(--text-h);">${{ number_format($platformExpenses, 0) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-xl-3">
        <div class="card">
            <div class="card-body" style="padding:13px 16px!important;">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:40px;height:40px;background:rgba(14,116,144,.1);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="fas fa-file-invoice-dollar" style="color:var(--ft-teal);font-size:15px;"></i>
                    </div>
                    <div>
                        <div style="font-size:10.5px;color:var(--text-muted);font-weight:700;text-transform:uppercase;letter-spacing:.4px;">Total Invoices</div>
                        <div style="font-size:20px;font-weight:800;color:var(--text-h);">{{ number_format($totalInvoices) }}</div>
                        <div style="font-size:11px;color:var(--text-muted);">{{ $paidInvoices }} paid</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-xl-3">
        <div class="card">
            <div class="card-body" style="padding:13px 16px!important;">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:40px;height:40px;background:rgba(139,92,246,.1);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="fas fa-piggy-bank" style="color:var(--ft-violet);font-size:15px;"></i>
                    </div>
                    <div>
                        <div style="font-size:10.5px;color:var(--text-muted);font-weight:700;text-transform:uppercase;letter-spacing:.4px;">Platform Savings</div>
                        <div style="font-size:20px;font-weight:800;color:var(--text-h);">${{ number_format($platformSavings, 0) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- ── Chart + Activity ─────────────────────────────────────────────────────── --}}
<div class="row g-3 mb-4">

    {{-- User Growth Chart --}}
    <div class="col-12 col-lg-6">
        <div class="card h-100">
            <div class="card-header">
                <h6 class="card-header-title">
                    <span class="card-header-icon bg-navy-soft">
                        <i class="fas fa-chart-line text-navy"></i>
                    </span>
                    User Registrations — {{ now()->year }}
                </h6>
            </div>
            <div class="card-body">
                <canvas id="userGrowthChart" style="max-height:220px;"></canvas>
            </div>
        </div>
    </div>

    {{-- Recent Activity --}}
    <div class="col-12 col-lg-6">
        <div class="card h-100">
            <div class="card-header">
                <h6 class="card-header-title">
                    <span class="card-header-icon bg-teal-soft">
                        <i class="fas fa-list-check" style="color:var(--ft-teal);"></i>
                    </span>
                    Recent Activity
                </h6>
                <a href="{{ route('admin.activity-logs.index') }}" class="btn btn-xs btn-outline-secondary">
                    View all <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            <div class="card-body p-0" style="max-height:270px;overflow-y:auto;">
                @forelse($recentActivity as $log)
                @php [$icon, $iconColor] = $log->icon; @endphp
                <div class="d-flex align-items-start gap-2 px-3 py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                    <div style="width:28px;height:28px;background:{{ $iconColor }}18;border-radius:7px;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:1px;">
                        <i class="{{ $icon }}" style="color:{{ $iconColor }};font-size:11px;"></i>
                    </div>
                    <div style="min-width:0;flex:1;">
                        <div style="font-size:12.5px;font-weight:600;color:var(--text-h);">{{ $log->description }}</div>
                        <div style="font-size:11px;color:var(--text-muted);">
                            {{ $log->user ? $log->user->name : 'System' }} · {{ $log->created_at->diffForHumans() }}
                        </div>
                    </div>
                </div>
                @empty
                <div class="empty-state" style="padding:40px 20px;">
                    <div class="empty-state-icon"><i class="fas fa-inbox"></i></div>
                    <p>No activity recorded yet</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

</div>

{{-- ── Recent Registrations + Top Users ────────────────────────────────────── --}}
<div class="row g-3">

    {{-- Recent Registrations --}}
    <div class="col-12 col-lg-7">
        <div class="card">
            <div class="card-header">
                <h6 class="card-header-title">
                    <span class="card-header-icon bg-emerald-soft">
                        <i class="fas fa-user-plus" style="color:#059669;"></i>
                    </span>
                    Recent Registrations
                </h6>
                <a href="{{ route('admin.users.index') }}" class="btn btn-xs btn-outline-secondary">
                    All users <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" style="font-size:13px;">
                        <thead>
                            <tr style="font-size:11px;text-transform:uppercase;letter-spacing:.4px;color:var(--text-muted);">
                                <th class="ps-3 py-2 fw-700" style="font-weight:700;">User</th>
                                <th class="py-2 fw-700" style="font-weight:700;">Role</th>
                                <th class="py-2 fw-700" style="font-weight:700;">Status</th>
                                <th class="py-2 fw-700" style="font-weight:700;">Joined</th>
                                <th class="pe-3 py-2"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentUsers as $u)
                            <tr>
                                <td class="ps-3 py-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <div style="width:30px;height:30px;background:var(--ft-navy);border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;color:#fff;font-size:12px;font-weight:700;">
                                            {{ strtoupper(substr($u->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div style="font-weight:600;color:var(--text-h);">{{ $u->name }}</div>
                                            <div style="font-size:11px;color:var(--text-muted);">{{ $u->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-2">
                                    @if($u->role === 'admin')
                                        <span class="badge" style="background:rgba(15,58,102,.12);color:var(--ft-navy);font-size:10.5px;">Admin</span>
                                    @else
                                        <span class="badge" style="background:rgba(14,116,144,.1);color:var(--ft-teal);font-size:10.5px;">User</span>
                                    @endif
                                </td>
                                <td class="py-2">
                                    @if($u->status === 'active')
                                        <span class="badge" style="background:rgba(5,150,105,.1);color:#059669;font-size:10.5px;">Active</span>
                                    @elseif($u->status === 'suspended')
                                        <span class="badge" style="background:rgba(244,63,94,.1);color:var(--ft-rose);font-size:10.5px;">Suspended</span>
                                    @else
                                        <span class="badge" style="background:rgba(100,116,139,.1);color:#64748B;font-size:10.5px;">{{ ucfirst($u->status ?? 'inactive') }}</span>
                                    @endif
                                </td>
                                <td class="py-2" style="color:var(--text-muted);font-size:11.5px;">
                                    {{ $u->created_at->format('M d, Y') }}
                                </td>
                                <td class="pe-3 py-2 text-end">
                                    <a href="{{ route('admin.users.show', $u->id) }}" class="btn btn-xs btn-outline-secondary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4" style="color:var(--text-muted);">No users found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Top Users by Income --}}
    <div class="col-12 col-lg-5">
        <div class="card">
            <div class="card-header">
                <h6 class="card-header-title">
                    <span class="card-header-icon" style="background:rgba(139,92,246,.1);">
                        <i class="fas fa-trophy" style="color:var(--ft-violet);"></i>
                    </span>
                    Top Users by Revenue
                </h6>
            </div>
            <div class="card-body p-0">
                @forelse($topUsers as $i => $u)
                <div class="d-flex align-items-center gap-3 px-3 py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                    <div style="width:24px;height:24px;background:{{ $i === 0 ? '#F59E0B' : ($i === 1 ? '#94A3B8' : ($i === 2 ? '#92400E' : 'var(--border)')) }};border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:800;color:{{ $i < 3 ? '#fff' : 'var(--text-muted)' }};flex-shrink:0;">
                        {{ $i + 1 }}
                    </div>
                    <div style="min-width:0;flex:1;">
                        <div style="font-size:13px;font-weight:600;color:var(--text-h);">{{ $u->name }}</div>
                        <div style="font-size:11px;color:var(--text-muted);">{{ $u->email }}</div>
                    </div>
                    <div style="font-size:13px;font-weight:700;color:#059669;flex-shrink:0;">
                        ${{ number_format($u->total_income ?? 0, 0) }}
                    </div>
                </div>
                @empty
                <div class="empty-state" style="padding:40px 20px;">
                    <div class="empty-state-icon"><i class="fas fa-inbox"></i></div>
                    <p>No data yet</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

</div>

@endsection

@section('scripts')
<script>
(function () {
    Chart.defaults.font.family = "'Plus Jakarta Sans', 'Segoe UI', system-ui, sans-serif";
    Chart.defaults.font.size   = 12;
    Chart.defaults.color       = '#64748B';

    var ctx = document.getElementById('userGrowthChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($regMonths) !!},
                datasets: [{
                    label: 'New Users',
                    data: {!! json_encode($regCounts) !!},
                    backgroundColor: 'rgba(14,116,144,.75)',
                    borderRadius: 5,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 },
                        grid: { color: '#F1F5F9', drawBorder: false },
                        border: { display: false },
                    },
                    x: {
                        grid: { display: false },
                        border: { display: false }
                    }
                }
            }
        });
    }
})();
</script>
@endsection
