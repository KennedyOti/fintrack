@extends('layouts.portal')

@section('title', 'Activity Logs — Admin')

@section('content')

{{-- ── Page Header ─────────────────────────────────────────────────────────── --}}
<div class="page-header">
    <div>
        <h1 class="page-title">Activity Logs</h1>
        <p class="page-subtitle">{{ number_format($logs->total()) }} log entries · Last updated {{ now()->format('H:i') }}</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('admin.dashboard') }}" class="btn btn-xs btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Admin Home
        </a>
        {{-- Clear old logs --}}
        <button class="btn btn-xs btn-outline-danger" type="button" data-bs-toggle="collapse" data-bs-target="#clearLogsPanel">
            <i class="fas fa-trash me-1"></i> Clear Old
        </button>
    </div>
</div>

{{-- Clear panel --}}
<div class="collapse mb-3" id="clearLogsPanel">
    <div class="card" style="border:1px solid rgba(244,63,94,.3);">
        <div class="card-body d-flex align-items-center gap-3" style="padding:14px 18px!important;">
            <form method="POST" action="{{ route('admin.activity-logs.clearOld') }}"
                  onsubmit="return confirm('This will permanently delete old logs. Continue?')">
                @csrf @method('DELETE')
                <div class="d-flex align-items-center gap-2">
                    <span style="font-size:13px;font-weight:600;">Delete entries older than</span>
                    <input type="number" name="days" value="90" min="7" class="form-control form-control-sm" style="width:80px;">
                    <span style="font-size:13px;">days</span>
                    <button type="submit" class="btn btn-sm btn-danger">
                        <i class="fas fa-trash me-1"></i> Delete
                    </button>
                </div>
            </form>
            <div style="font-size:12px;color:var(--text-muted);">
                <i class="fas fa-triangle-exclamation me-1;color:var(--ft-amber);"></i>
                This action is permanent and cannot be undone.
            </div>
        </div>
    </div>
</div>

{{-- ── Filters ──────────────────────────────────────────────────────────────── --}}
<div class="card mb-4">
    <div class="card-body" style="padding:14px 18px!important;">
        <form method="GET" action="{{ route('admin.activity-logs.index') }}" class="row g-2 align-items-end">

            <div class="col-12 col-sm-6 col-lg-3">
                <label class="form-label" style="font-size:12px;font-weight:600;">User</label>
                <select class="form-select form-select-sm" name="user_id">
                    <option value="">All Users</option>
                    @foreach($users as $u)
                    <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>
                        {{ $u->name }} ({{ $u->email }})
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="col-12 col-sm-6 col-lg-3">
                <label class="form-label" style="font-size:12px;font-weight:600;">Action</label>
                <input type="text" class="form-control form-control-sm" name="action"
                       value="{{ request('action') }}" placeholder="e.g. admin.user or login">
            </div>

            <div class="col-6 col-lg-2">
                <label class="form-label" style="font-size:12px;font-weight:600;">From</label>
                <input type="date" class="form-control form-control-sm" name="date_from"
                       value="{{ request('date_from') }}">
            </div>

            <div class="col-6 col-lg-2">
                <label class="form-label" style="font-size:12px;font-weight:600;">To</label>
                <input type="date" class="form-control form-control-sm" name="date_to"
                       value="{{ request('date_to') }}">
            </div>

            <div class="col-12 col-lg-auto d-flex gap-2">
                <button type="submit" class="btn btn-sm" style="background:var(--ft-navy);color:#fff;">
                    <i class="fas fa-filter me-1"></i> Filter
                </button>
                <a href="{{ route('admin.activity-logs.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-xmark"></i>
                </a>
            </div>

        </form>
    </div>
</div>

{{-- ── Logs Table ───────────────────────────────────────────────────────────── --}}
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" style="font-size:12.5px;">
                <thead>
                    <tr style="font-size:11px;text-transform:uppercase;letter-spacing:.4px;color:var(--text-muted);background:var(--surface);">
                        <th class="ps-3 py-2" style="font-weight:700;">When</th>
                        <th class="py-2" style="font-weight:700;">User</th>
                        <th class="py-2" style="font-weight:700;">Action</th>
                        <th class="py-2" style="font-weight:700;">Description</th>
                        <th class="pe-3 py-2" style="font-weight:700;">IP Address</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    @php [$icon, $iconColor] = $log->icon; @endphp
                    <tr>
                        <td class="ps-3 py-2" style="white-space:nowrap;color:var(--text-muted);font-size:11.5px;">
                            <div style="font-weight:600;color:var(--text-h);">{{ $log->created_at->format('M d, Y') }}</div>
                            <div>{{ $log->created_at->format('H:i:s') }}</div>
                        </td>
                        <td class="py-2">
                            @if($log->user)
                                <div style="font-weight:600;color:var(--text-h);">{{ $log->user->name }}</div>
                                <div style="font-size:11px;color:var(--text-muted);">{{ $log->user->email }}</div>
                            @else
                                <span style="color:var(--text-muted);">System</span>
                            @endif
                        </td>
                        <td class="py-2">
                            <div class="d-flex align-items-center gap-2">
                                <div style="width:24px;height:24px;background:{{ $iconColor }}18;border-radius:6px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <i class="{{ $icon }}" style="color:{{ $iconColor }};font-size:10px;"></i>
                                </div>
                                <span class="badge" style="background:var(--surface);color:var(--text-muted);font-size:9.5px;border:1px solid var(--border);font-weight:600;">
                                    {{ $log->action }}
                                </span>
                            </div>
                        </td>
                        <td class="py-2" style="max-width:320px;">
                            <div style="color:var(--text-h);">{{ $log->description }}</div>
                            @if($log->properties)
                                <div style="font-size:11px;color:var(--text-muted);margin-top:2px;">
                                    {{ implode(', ', array_map(fn($k, $v) => is_array($v) ? "{$k}: {$v['from']}→{$v['to']}" : $k, array_keys($log->properties), $log->properties)) }}
                                </div>
                            @endif
                        </td>
                        <td class="pe-3 py-2" style="color:var(--text-muted);font-size:11.5px;white-space:nowrap;">
                            {{ $log->ip_address ?: '—' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5" style="color:var(--text-muted);">
                            <i class="fas fa-list-check" style="font-size:28px;display:block;margin-bottom:8px;"></i>
                            No activity logs found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($logs->hasPages())
    <div class="card-footer d-flex justify-content-between align-items-center" style="padding:10px 16px;">
        <div style="font-size:12px;color:var(--text-muted);">
            Showing {{ $logs->firstItem() }}–{{ $logs->lastItem() }} of {{ number_format($logs->total()) }} entries
        </div>
        <div>{{ $logs->links() }}</div>
    </div>
    @endif
</div>

@endsection
