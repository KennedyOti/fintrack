@extends('layouts.portal')

@section('title', 'User Management — Admin')

@section('content')

{{-- ── Page Header ─────────────────────────────────────────────────────────── --}}
<div class="page-header">
    <div>
        <h1 class="page-title">User Management</h1>
        <p class="page-subtitle">
            {{ $users->total() }} {{ Str::plural('user', $users->total()) }} found
            @if(request('trashed')) · Showing deleted @endif
        </p>
    </div>
    <div class="page-actions">
        <a href="{{ route('admin.dashboard') }}" class="btn btn-xs btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Admin Home
        </a>
    </div>
</div>

{{-- ── Filters ──────────────────────────────────────────────────────────────── --}}
<div class="card mb-4">
    <div class="card-body" style="padding:14px 18px!important;">
        <form method="GET" action="{{ route('admin.users.index') }}" class="row g-2 align-items-end">

            <div class="col-12 col-sm-5 col-lg-4">
                <label class="form-label" style="font-size:12px;font-weight:600;">Search</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control" name="search"
                           value="{{ request('search') }}" placeholder="Name, email, business…">
                </div>
            </div>

            <div class="col-6 col-sm-3 col-lg-2">
                <label class="form-label" style="font-size:12px;font-weight:600;">Role</label>
                <select class="form-select form-select-sm" name="role">
                    <option value="">All Roles</option>
                    <option value="admin"  {{ request('role') === 'admin'  ? 'selected' : '' }}>Admin</option>
                    <option value="user"   {{ request('role') === 'user'   ? 'selected' : '' }}>User</option>
                </select>
            </div>

            <div class="col-6 col-sm-3 col-lg-2">
                <label class="form-label" style="font-size:12px;font-weight:600;">Status</label>
                <select class="form-select form-select-sm" name="status">
                    <option value="">All Statuses</option>
                    <option value="active"    {{ request('status') === 'active'    ? 'selected' : '' }}>Active</option>
                    <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                    <option value="inactive"  {{ request('status') === 'inactive'  ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <div class="col-6 col-sm-auto col-lg-auto">
                <label class="form-label" style="font-size:12px;font-weight:600;">Deleted</label>
                <div class="form-check form-switch mt-1">
                    <input class="form-check-input" type="checkbox" name="trashed" value="1"
                           id="trashedToggle" {{ request('trashed') ? 'checked' : '' }}>
                    <label class="form-check-label" for="trashedToggle" style="font-size:12px;">Show only deleted</label>
                </div>
            </div>

            <div class="col-6 col-sm-auto col-lg-auto d-flex gap-2">
                <button type="submit" class="btn btn-sm" style="background:var(--ft-navy);color:#fff;">
                    <i class="fas fa-filter me-1"></i> Filter
                </button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-xmark"></i>
                </a>
            </div>

        </form>
    </div>
</div>

{{-- ── Users Table ──────────────────────────────────────────────────────────── --}}
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" style="font-size:13px;">
                <thead>
                    <tr style="font-size:11px;text-transform:uppercase;letter-spacing:.4px;color:var(--text-muted);background:var(--surface);">
                        <th class="ps-3 py-2" style="font-weight:700;">User</th>
                        <th class="py-2" style="font-weight:700;">Business</th>
                        <th class="py-2" style="font-weight:700;">Role</th>
                        <th class="py-2" style="font-weight:700;">Status</th>
                        <th class="py-2" style="font-weight:700;">Joined</th>
                        <th class="py-2" style="font-weight:700;">Last Login</th>
                        <th class="pe-3 py-2 text-end" style="font-weight:700;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $u)
                    <tr class="{{ $u->trashed() ? 'table-danger' : '' }}">
                        <td class="ps-3 py-2">
                            <div class="d-flex align-items-center gap-2">
                                <div style="width:32px;height:32px;background:{{ $u->role === 'admin' ? 'var(--ft-navy)' : 'var(--ft-teal)' }};border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;color:#fff;font-size:13px;font-weight:700;">
                                    {{ strtoupper(substr($u->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div style="font-weight:600;color:var(--text-h);">
                                        {{ $u->name }}
                                        @if($u->id === Auth::id())
                                            <span class="badge ms-1" style="background:rgba(14,116,144,.1);color:var(--ft-teal);font-size:9px;">You</span>
                                        @endif
                                        @if($u->trashed())
                                            <span class="badge ms-1" style="background:rgba(244,63,94,.1);color:var(--ft-rose);font-size:9px;">Deleted</span>
                                        @endif
                                    </div>
                                    <div style="font-size:11px;color:var(--text-muted);">{{ $u->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="py-2" style="color:var(--text-muted);font-size:12px;">
                            {{ $u->business_name ?: '—' }}
                        </td>
                        <td class="py-2">
                            @if($u->role === 'admin')
                                <span class="badge" style="background:rgba(15,58,102,.12);color:var(--ft-navy);font-size:10.5px;">
                                    <i class="fas fa-shield-halved me-1"></i>Admin
                                </span>
                            @else
                                <span class="badge" style="background:rgba(14,116,144,.1);color:var(--ft-teal);font-size:10.5px;">User</span>
                            @endif
                        </td>
                        <td class="py-2">
                            @if($u->status === 'active')
                                <span class="badge" style="background:rgba(5,150,105,.1);color:#059669;font-size:10.5px;">
                                    <i class="fas fa-circle" style="font-size:6px;"></i> Active
                                </span>
                            @elseif($u->status === 'suspended')
                                <span class="badge" style="background:rgba(244,63,94,.1);color:var(--ft-rose);font-size:10.5px;">
                                    <i class="fas fa-ban me-1"></i>Suspended
                                </span>
                            @else
                                <span class="badge" style="background:rgba(100,116,139,.1);color:#64748B;font-size:10.5px;">
                                    {{ ucfirst($u->status ?? 'inactive') }}
                                </span>
                            @endif
                        </td>
                        <td class="py-2" style="color:var(--text-muted);font-size:11.5px;">
                            {{ $u->created_at->format('M d, Y') }}
                        </td>
                        <td class="py-2" style="color:var(--text-muted);font-size:11.5px;">
                            {{ $u->last_login_at ? $u->last_login_at->diffForHumans() : 'Never' }}
                        </td>
                        <td class="pe-3 py-2">
                            <div class="d-flex justify-content-end gap-1">
                                @if($u->trashed())
                                    {{-- Restore --}}
                                    <form method="POST" action="{{ route('admin.users.restore', $u->id) }}">
                                        @csrf
                                        <button class="btn btn-xs btn-outline-success" title="Restore user">
                                            <i class="fas fa-rotate-left"></i>
                                        </button>
                                    </form>
                                @else
                                    <a href="{{ route('admin.users.show', $u->id) }}" class="btn btn-xs btn-outline-secondary" title="View profile">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.users.edit', $u->id) }}" class="btn btn-xs btn-outline-secondary" title="Edit user">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    @if($u->id !== Auth::id())
                                        {{-- Toggle status --}}
                                        <form method="POST" action="{{ route('admin.users.toggleStatus', $u->id) }}" class="d-inline">
                                            @csrf @method('PATCH')
                                            <button class="btn btn-xs {{ $u->status === 'suspended' ? 'btn-outline-success' : 'btn-outline-warning' }}"
                                                    title="{{ $u->status === 'suspended' ? 'Activate' : 'Suspend' }}">
                                                <i class="fas fa-{{ $u->status === 'suspended' ? 'circle-check' : 'ban' }}"></i>
                                            </button>
                                        </form>
                                        {{-- Delete --}}
                                        <form method="POST" action="{{ route('admin.users.destroy', $u->id) }}"
                                              onsubmit="return confirm('Delete user {{ addslashes($u->name) }}? This is reversible.')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-xs btn-outline-danger" title="Delete user">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5" style="color:var(--text-muted);">
                            <i class="fas fa-users-slash" style="font-size:28px;display:block;margin-bottom:8px;"></i>
                            No users found matching your filters.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($users->hasPages())
    <div class="card-footer d-flex justify-content-between align-items-center" style="padding:10px 16px;">
        <div style="font-size:12px;color:var(--text-muted);">
            Showing {{ $users->firstItem() }}–{{ $users->lastItem() }} of {{ $users->total() }}
        </div>
        <div>{{ $users->links() }}</div>
    </div>
    @endif
</div>

@endsection
