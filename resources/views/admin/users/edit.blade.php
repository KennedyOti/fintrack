@extends('layouts.portal')

@section('title', 'Edit User: ' . $user->name . ' — Admin')

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title">Edit User</h1>
        <p class="page-subtitle">{{ $user->name }} · {{ $user->email }}</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-xs btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Profile
        </a>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-12 col-lg-8 col-xl-6">
        <form method="POST" action="{{ route('admin.users.update', $user->id) }}">
            @csrf
            @method('PUT')

            {{-- ── Profile Details ──────────────────────────────────────────── --}}
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="card-header-title">
                        <span class="card-header-icon bg-navy-soft">
                            <i class="fas fa-user text-navy"></i>
                        </span>
                        Profile Details
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12 col-sm-6">
                            <label class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   name="name" value="{{ old('name', $user->name) }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label">Email Address <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                   name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label">Phone</label>
                            <input type="text" class="form-control"
                                   name="phone" value="{{ old('phone', $user->phone) }}" maxlength="30">
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label">Business Name</label>
                            <input type="text" class="form-control"
                                   name="business_name" value="{{ old('business_name', $user->business_name) }}" maxlength="255">
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Access Control ───────────────────────────────────────────── --}}
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="card-header-title">
                        <span class="card-header-icon" style="background:rgba(15,58,102,.1);">
                            <i class="fas fa-shield-halved" style="color:var(--ft-navy);"></i>
                        </span>
                        Role &amp; Access
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12 col-sm-6">
                            <label class="form-label">Role <span class="text-danger">*</span></label>
                            <select class="form-select @error('role') is-invalid @enderror" name="role"
                                    {{ $user->id === Auth::id() ? 'disabled' : '' }}>
                                <option value="user"  {{ old('role', $user->role) === 'user'  ? 'selected' : '' }}>User</option>
                                <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Administrator</option>
                            </select>
                            @if($user->id === Auth::id())
                                <input type="hidden" name="role" value="{{ $user->role }}">
                                <div class="form-text text-warning">
                                    <i class="fas fa-triangle-exclamation me-1"></i>You cannot change your own role.
                                </div>
                            @endif
                            @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label">Account Status <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" name="status"
                                    {{ $user->id === Auth::id() ? 'disabled' : '' }}>
                                <option value="active"    {{ old('status', $user->status) === 'active'    ? 'selected' : '' }}>Active</option>
                                <option value="suspended" {{ old('status', $user->status) === 'suspended' ? 'selected' : '' }}>Suspended</option>
                                <option value="inactive"  {{ old('status', $user->status) === 'inactive'  ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @if($user->id === Auth::id())
                                <input type="hidden" name="status" value="{{ $user->status }}">
                                <div class="form-text text-warning">
                                    <i class="fas fa-triangle-exclamation me-1"></i>You cannot change your own status.
                                </div>
                            @endif
                            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="alert" style="background:rgba(14,116,144,.07);border:1px solid rgba(14,116,144,.2);border-radius:var(--r-md);padding:10px 14px;margin-top:12px;font-size:12.5px;color:var(--ft-teal);">
                        <i class="fas fa-circle-info me-2"></i>
                        <strong>Suspended</strong> users are immediately logged out and blocked from signing in.
                        <strong>Admin</strong> users have full platform access including this panel.
                    </div>
                </div>
            </div>

            {{-- ── Password Change ──────────────────────────────────────────── --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-header-title">
                        <span class="card-header-icon" style="background:rgba(139,92,246,.1);">
                            <i class="fas fa-key" style="color:var(--ft-violet);"></i>
                        </span>
                        Change Password
                    </h6>
                    <span style="font-size:12px;color:var(--text-muted);">Leave blank to keep current password</span>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12 col-sm-6">
                            <label class="form-label">New Password</label>
                            <input type="password" class="form-control @error('new_password') is-invalid @enderror"
                                   name="new_password" minlength="8" autocomplete="new-password">
                            @error('new_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control"
                                   name="new_password_confirmation" autocomplete="new-password">
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2 justify-content-end">
                <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-outline-secondary">
                    Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> Save Changes
                </button>
            </div>

        </form>
    </div>
</div>

@endsection
