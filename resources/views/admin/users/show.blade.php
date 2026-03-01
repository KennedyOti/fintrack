@extends('layouts.portal')

@section('title', 'User: ' . $user->name . ' — Admin')

@section('content')

{{-- ── Page Header ─────────────────────────────────────────────────────────── --}}
<div class="page-header">
    <div>
        <h1 class="page-title">{{ $user->name }}</h1>
        <p class="page-subtitle">
            User ID #{{ $user->id }}
            @if($user->trashed())
                · <span style="color:var(--ft-rose);">Deleted {{ $user->deleted_at->diffForHumans() }}</span>
            @endif
        </p>
    </div>
    <div class="page-actions">
        <a href="{{ route('admin.users.index') }}" class="btn btn-xs btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> All Users
        </a>
        @if($user->trashed())
            <form method="POST" action="{{ route('admin.users.restore', $user->id) }}" class="d-inline">
                @csrf
                <button class="btn btn-sm btn-success">
                    <i class="fas fa-rotate-left me-1"></i> Restore Account
                </button>
            </form>
        @else
            <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm" style="background:var(--ft-navy);color:#fff;">
                <i class="fas fa-pen me-1"></i> Edit User
            </a>
            @if($user->id !== Auth::id())
                <form method="POST" action="{{ route('admin.users.toggleStatus', $user->id) }}" class="d-inline">
                    @csrf @method('PATCH')
                    <button class="btn btn-sm {{ $user->status === 'suspended' ? 'btn-success' : 'btn-warning' }}">
                        <i class="fas fa-{{ $user->status === 'suspended' ? 'circle-check' : 'ban' }} me-1"></i>
                        {{ $user->status === 'suspended' ? 'Activate' : 'Suspend' }}
                    </button>
                </form>
            @endif
        @endif
    </div>
</div>

<div class="row g-3">

    {{-- ── Profile Card ─────────────────────────────────────────────────────── --}}
    <div class="col-12 col-lg-4">
        <div class="card">
            <div class="card-body" style="padding:20px;">
                {{-- Avatar --}}
                <div class="text-center mb-3">
                    @if($user->google_avatar)
                        <img src="{{ $user->google_avatar }}" alt="Avatar"
                             style="width:72px;height:72px;border-radius:50%;object-fit:cover;">
                    @else
                        <div style="width:72px;height:72px;background:var(--ft-navy);border-radius:50%;display:inline-flex;align-items:center;justify-content:center;color:#fff;font-size:28px;font-weight:800;">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    @endif
                    <div style="margin-top:10px;font-size:17px;font-weight:700;color:var(--text-h);">{{ $user->name }}</div>
                    <div style="font-size:12.5px;color:var(--text-muted);">{{ $user->email }}</div>
                    <div class="d-flex justify-content-center gap-2 mt-2">
                        @if($user->role === 'admin')
                            <span class="badge" style="background:rgba(15,58,102,.12);color:var(--ft-navy);">
                                <i class="fas fa-shield-halved me-1"></i>Admin
                            </span>
                        @else
                            <span class="badge" style="background:rgba(14,116,144,.1);color:var(--ft-teal);">User</span>
                        @endif
                        @if($user->status === 'active')
                            <span class="badge" style="background:rgba(5,150,105,.1);color:#059669;">Active</span>
                        @elseif($user->status === 'suspended')
                            <span class="badge" style="background:rgba(244,63,94,.1);color:var(--ft-rose);">Suspended</span>
                        @else
                            <span class="badge" style="background:rgba(100,116,139,.1);color:#64748B;">{{ ucfirst($user->status ?? 'inactive') }}</span>
                        @endif
                    </div>
                </div>

                <hr style="border-color:var(--border);">

                {{-- Profile Info --}}
                <div class="d-flex flex-column gap-2" style="font-size:13px;">
                    @if($user->business_name)
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-building" style="width:16px;color:var(--ft-teal);"></i>
                        <span style="color:var(--text-muted);">{{ $user->business_name }}</span>
                    </div>
                    @endif
                    @if($user->phone)
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-phone" style="width:16px;color:var(--ft-teal);"></i>
                        <span style="color:var(--text-muted);">{{ $user->phone }}</span>
                    </div>
                    @endif
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-calendar" style="width:16px;color:var(--ft-teal);"></i>
                        <span style="color:var(--text-muted);">Joined {{ $user->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-clock" style="width:16px;color:var(--ft-teal);"></i>
                        <span style="color:var(--text-muted);">
                            Last login: {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}
                        </span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-coins" style="width:16px;color:var(--ft-teal);"></i>
                        <span style="color:var(--text-muted);">{{ $user->currency_code ?: 'USD' }}</span>
                    </div>
                    @if($user->two_factor_enabled)
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-shield-check" style="width:16px;color:#059669;"></i>
                        <span style="color:#059669;font-weight:600;">2FA Enabled</span>
                    </div>
                    @endif
                    @if($user->google_id)
                    <div class="d-flex align-items-center gap-2">
                        <i class="fab fa-google" style="width:16px;color:#EA4335;"></i>
                        <span style="color:var(--text-muted);">Google account linked</span>
                    </div>
                    @endif
                </div>

                @if(!$user->trashed() && $user->id !== Auth::id())
                <hr style="border-color:var(--border);">
                {{-- Reset Password --}}
                <button class="btn btn-xs btn-outline-secondary w-100" type="button"
                        data-bs-toggle="collapse" data-bs-target="#resetPwForm">
                    <i class="fas fa-key me-1"></i> Reset Password
                </button>
                <div class="collapse mt-2" id="resetPwForm">
                    <form method="POST" action="{{ route('admin.users.resetPassword', $user->id) }}">
                        @csrf
                        <div class="mb-2">
                            <input type="password" class="form-control form-control-sm"
                                   name="password" placeholder="New password" required minlength="8">
                        </div>
                        <div class="mb-2">
                            <input type="password" class="form-control form-control-sm"
                                   name="password_confirmation" placeholder="Confirm password" required>
                        </div>
                        <button type="submit" class="btn btn-xs w-100" style="background:var(--ft-rose);color:#fff;">
                            Set New Password
                        </button>
                    </form>
                </div>

                <div class="mt-2">
                    <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}"
                          onsubmit="return confirm('Permanently delete this user? This can be restored.')">
                        @csrf @method('DELETE')
                        <button class="btn btn-xs btn-outline-danger w-100">
                            <i class="fas fa-trash me-1"></i> Delete User
                        </button>
                    </form>
                </div>
                @endif

            </div>
        </div>
    </div>

    {{-- ── Right Column ─────────────────────────────────────────────────────── --}}
    <div class="col-12 col-lg-8">

        {{-- Financial Stats --}}
        <div class="row g-3 mb-3">
            <div class="col-4">
                <div class="card text-center">
                    <div class="card-body" style="padding:14px!important;">
                        <div style="font-size:11px;color:var(--text-muted);font-weight:700;text-transform:uppercase;letter-spacing:.4px;margin-bottom:4px;">Income</div>
                        <div style="font-size:20px;font-weight:800;color:#059669;">${{ number_format($stats['income'], 0) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="card text-center">
                    <div class="card-body" style="padding:14px!important;">
                        <div style="font-size:11px;color:var(--text-muted);font-weight:700;text-transform:uppercase;letter-spacing:.4px;margin-bottom:4px;">Expenses</div>
                        <div style="font-size:20px;font-weight:800;color:var(--ft-rose);">${{ number_format($stats['expenses'], 0) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="card text-center">
                    <div class="card-body" style="padding:14px!important;">
                        <div style="font-size:11px;color:var(--text-muted);font-weight:700;text-transform:uppercase;letter-spacing:.4px;margin-bottom:4px;">Savings</div>
                        <div style="font-size:20px;font-weight:800;color:var(--ft-teal);">${{ number_format($stats['savings'], 0) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="card text-center">
                    <div class="card-body" style="padding:14px!important;">
                        <div style="font-size:11px;color:var(--text-muted);font-weight:700;text-transform:uppercase;letter-spacing:.4px;margin-bottom:4px;">Invoices</div>
                        <div style="font-size:20px;font-weight:800;color:var(--text-h);">{{ $stats['invoices'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="card text-center">
                    <div class="card-body" style="padding:14px!important;">
                        <div style="font-size:11px;color:var(--text-muted);font-weight:700;text-transform:uppercase;letter-spacing:.4px;margin-bottom:4px;">Clients</div>
                        <div style="font-size:20px;font-weight:800;color:var(--text-h);">{{ $stats['clients'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="card text-center">
                    <div class="card-body" style="padding:14px!important;">
                        <div style="font-size:11px;color:var(--text-muted);font-weight:700;text-transform:uppercase;letter-spacing:.4px;margin-bottom:4px;">Projects</div>
                        <div style="font-size:20px;font-weight:800;color:var(--text-h);">{{ $stats['projects'] }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Activity Logs for this user --}}
        <div class="card">
            <div class="card-header">
                <h6 class="card-header-title">
                    <span class="card-header-icon bg-teal-soft">
                        <i class="fas fa-list-check" style="color:var(--ft-teal);"></i>
                    </span>
                    Activity Log
                </h6>
                <a href="{{ route('admin.activity-logs.index', ['user_id' => $user->id]) }}"
                   class="btn btn-xs btn-outline-secondary">
                    View all <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            <div class="card-body p-0">
                @forelse($activityLogs as $log)
                @php [$icon, $iconColor] = $log->icon; @endphp
                <div class="d-flex align-items-start gap-3 px-3 py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                    <div style="width:30px;height:30px;background:{{ $iconColor }}18;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:1px;">
                        <i class="{{ $icon }}" style="color:{{ $iconColor }};font-size:11px;"></i>
                    </div>
                    <div style="min-width:0;flex:1;">
                        <div style="font-size:13px;font-weight:600;color:var(--text-h);">{{ $log->description }}</div>
                        <div style="font-size:11px;color:var(--text-muted);">
                            <span class="badge me-1" style="background:var(--surface);color:var(--text-muted);font-size:9.5px;border:1px solid var(--border);">{{ $log->action }}</span>
                            {{ $log->created_at->format('M d, Y H:i') }} · {{ $log->ip_address }}
                        </div>
                    </div>
                </div>
                @empty
                <div class="empty-state" style="padding:40px 20px;">
                    <div class="empty-state-icon"><i class="fas fa-inbox"></i></div>
                    <p>No activity logged for this user.</p>
                </div>
                @endforelse
            </div>
        </div>

    </div>
</div>

@endsection
