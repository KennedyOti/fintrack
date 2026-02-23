@extends('layouts.portal')

@section('title', 'Savings - FinTrack')

@section('content')
<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">Savings Accounts</h4>
        <p class="text-muted mb-0">Manage your savings goals and accounts</p>
    </div>
    <a href="{{ route('savings.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i> New Savings Account
    </a>
</div>

<!-- Filters -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-6">
                <input type="text" name="search" class="form-control" placeholder="Search accounts..." value="{{ $search }}">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="active" {{ $status == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="archived" {{ $status == 'archived' ? 'selected' : '' }}>Archived</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search me-1"></i> Search
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Summary Cards -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-0">Total Savings</p>
                        <h4 class="mb-0">
                            {{ $accounts->sum('current_balance') > 0 ? number_format($accounts->sum('current_balance'), 2) : '0.00' }}
                        </h4>
                    </div>
                    <div class="text-primary">
                        <i class="fas fa-piggy-bank fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-0">Active Accounts</p>
                        <h4 class="mb-0">{{ $accounts->where('status', 'active')->count() }}</h4>
                    </div>
                    <div class="text-success">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-0">Net Income Available</p>
                        <h4 class="mb-0 {{ $netIncome > 0 ? 'text-success' : 'text-danger' }}">
                            {{ number_format($netIncome, 2) }}
                        </h4>
                    </div>
                    <div class="{{ $netIncome > 0 ? 'text-success' : 'text-danger' }}">
                        <i class="fas fa-wallet fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Save Section (only show if there's positive net income) -->
@if($netIncome > 0 && $accounts->where('status', 'active')->count() > 0)
@php
$totalSavings = $accounts->sum('current_balance');
$availableForSavings = $netIncome - $totalSavings;
@endphp
@if($availableForSavings > 0)
<div class="card border-0 shadow-sm mb-4 bg-success bg-opacity-10">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-1"><i class="fas fa-bolt me-2"></i>Quick Save</h5>
                <p class="text-muted mb-0">You have ${{ number_format($availableForSavings, 2) }} available to save from your positive net income!</p>
            </div>
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#quickSaveModal">
                <i class="fas fa-plus me-1"></i> Quick Save
            </button>
        </div>
    </div>
</div>

<!-- Quick Save Modal -->
<div class="modal fade" id="quickSaveModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Quick Save</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('savings.quickSave') }}">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        You can save up to ${{ number_format($availableForSavings, 2) }} from your positive net income.
                    </div>
                    <div class="mb-3">
                        <label for="quick_save_account" class="form-label">Savings Account</label>
                        <select name="savings_account_id" id="quick_save_account" class="form-select" required>
                            <option value="">Select an account</option>
                            @foreach($accounts->where('status', 'active') as $account)
                                <option value="{{ $account->id }}">{{ $account->name }} (Current: ${{ number_format($account->current_balance, 2) }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="quick_save_amount" class="form-label">Amount to Save</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" name="amount" id="quick_save_amount" class="form-control" 
                                   min="0.01" max="{{ $availableForSavings }}" step="0.01" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="quick_save_notes" class="form-label">Notes (Optional)</label>
                        <input type="text" name="notes" id="quick_save_notes" class="form-control" placeholder="e.g., Weekly savings">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Save Now</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endif

<!-- Savings Accounts Table -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        @if($accounts->count() > 0)
            <table class="table table-hover table-nowrap mb-0">
                <thead>
                    <tr>
                        <th>Account Name</th>
                        <th>Description</th>
                        <th class="text-end">Target</th>
                        <th class="text-end">Balance</th>
                        <th style="width: 150px;">Progress</th>
                        <th>Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($accounts as $account)
                    <tr>
                        <td class="fw-semibold">
                            <a href="{{ route('savings.show', ['saving' => $account->id]) }}" class="text-decoration-none">
                                {{ $account->name }}
                            </a>
                        </td>
                        <td class="text-muted">{{ $account->description ? substr($account->description, 0, 30) . (strlen($account->description) > 30 ? '...' : '') : '-' }}</td>
                        <td class="text-end">{{ $account->target_amount ? number_format($account->target_amount, 2) : '-' }}</td>
                        <td class="text-end fw-semibold text-success">{{ number_format($account->current_balance, 2) }}</td>
                        <td>
                            @if($account->target_amount)
                            <div class="d-flex align-items-center gap-2">
                                <div class="progress flex-grow-1" style="height: 8px;">
                                    <div class="progress-bar bg-success" role="progressbar" 
                                         style="width: {{ $account->progressPercentage() }}%"></div>
                                </div>
                                <small class="text-muted">{{ number_format($account->progressPercentage(), 0) }}%</small>
                            </div>
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-{{ $account->status == 'active' ? 'success' : 'secondary' }}">
                                {{ ucfirst($account->status) }}
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light" data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="{{ route('savings.show', ['saving' => $account->id]) }}">
                                        <i class="fas fa-eye me-2"></i>View
                                    </a></li>
                                    <li><a class="dropdown-item" href="{{ route('savings.edit', ['saving' => $account->id]) }}">
                                        <i class="fas fa-edit me-2"></i>Edit
                                    </a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('savings.destroy', ['saving' => $account->id]) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger" 
                                                    onclick="return confirm('Are you sure you want to delete this savings account?')">
                                                <i class="fas fa-trash me-2"></i>Delete
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
        <div class="text-center py-5">
            <div class="mb-3">
                <i class="fas fa-piggy-bank fa-4x text-muted"></i>
            </div>
            <h5>No savings accounts yet</h5>
            <p class="text-muted mb-3">Start saving by creating your first savings account.</p>
            <a href="{{ route('savings.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Create Savings Account
            </a>
        </div>
        @endif
    </div>
</div>

<!-- Pagination -->
@if($accounts->hasPages())
<div class="mt-4">
    {{ $accounts->links() }}
</div>
@endif
@endsection
