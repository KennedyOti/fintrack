@extends('layouts.portal')

@section('title', $savings->name . ' - FinTrack')

@section('content')
<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">{{ $savings->name }}</h4>
        <p class="text-muted mb-0">Savings Account Details</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('savings.edit', $savings->id) }}" class="btn btn-outline-primary">
            <i class="fas fa-edit me-1"></i> Edit
        </a>
        <a href="{{ route('savings.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Savings
        </a>
    </div>
</div>

<!-- Account Summary Cards -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-0">Current Balance</p>
                        <h3 class="mb-0 text-success">${{ number_format($savings->current_balance, 2) }}</h3>
                    </div>
                    <div class="text-success">
                        <i class="fas fa-wallet fa-2x"></i>
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
                        <p class="text-muted mb-0">Target Amount</p>
                        <h3 class="mb-0">{{ $savings->target_amount ? '$' . number_format($savings->target_amount, 2) : '-' }}</h3>
                    </div>
                    <div class="text-info">
                        <i class="fas fa-bullseye fa-2x"></i>
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
                        <p class="text-muted mb-0">Remaining to Goal</p>
                        <h3 class="mb-0">
                            @if($savings->target_amount)
                                ${{ number_format(max(0, $savings->target_amount - $savings->current_balance), 2) }}
                            @else
                                -
                            @endif
                        </h3>
                    </div>
                    <div class="text-warning">
                        <i class="fas fa-chart-line fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Progress Bar (if target is set) -->
@if($savings->target_amount)
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="fw-semibold">Savings Progress</span>
            <span class="text-muted">{{ number_format($savings->progressPercentage(), 1) }}%</span>
        </div>
        <div class="progress" style="height: 20px;">
            <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" 
                 role="progressbar" 
                 style="width: {{ $savings->progressPercentage() }}%">
                {{ $savings->progressPercentage() >= 100 ? 'Goal Reached!' : '' }}
            </div>
        </div>
        @if($savings->progressPercentage() >= 100)
        <div class="mt-2 text-success">
            <i class="fas fa-trophy me-1"></i> Congratulations! You've reached your savings goal!
        </div>
        @endif
    </div>
</div>
@endif

<div class="row">
    <!-- Account Details -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent">
                <h5 class="mb-0">Account Details</h5>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4">Status</dt>
                    <dd class="col-sm-8">
                        <span class="badge bg-{{ $savings->status == 'active' ? 'success' : 'secondary' }}">
                            {{ ucfirst($savings->status) }}
                        </span>
                    </dd>
                    
                    <dt class="col-sm-4">Description</dt>
                    <dd class="col-sm-8">{{ $savings->description ?? 'No description' }}</dd>
                    
                    <dt class="col-sm-4">Created</dt>
                    <dd class="col-sm-8">{{ $savings->created_at->format('M d, Y') }}</dd>
                    
                    <dt class="col-sm-4">Updated</dt>
                    <dd class="col-sm-8">{{ $savings->updated_at->format('M d, Y') }}</dd>
                </dl>
            </div>
        </div>

        <!-- Deposit/Withdraw Forms -->
        <div class="row">
            <!-- Deposit Form -->
            <div class="col-12 mb-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent">
                        <h5 class="mb-0 text-success"><i class="fas fa-plus-circle me-1"></i> Deposit (From Income)</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('savings.deposit', $savings->id) }}">
                            @csrf
                            <div class="mb-2">
                                <label for="deposit_income" class="form-label">Select Income Source</label>
                                <select name="income_id" id="deposit_income" class="form-select" required>
                                    <option value="">Select income...</option>
                                    @forelse($availableIncome as $income)
                                        @php
                                            $savedFromThis = $savings->transactions()
                                                ->where('income_id', $income->id)
                                                ->where('type', 'deposit')
                                                ->sum('amount');
                                            $availableFromThis = $income->amount - $savedFromThis;
                                        @endphp
                                        @if($availableFromThis > 0)
                                            <option value="{{ $income->id }}">
                                                {{ $income->category->name ?? 'Uncategorized' }} - ${{ number_format($income->amount, 2) }} ({{ $income->income_date->format('M d') }})
                                                [Available: ${{ number_format($availableFromThis, 2) }}]
                                            </option>
                                        @endif
                                    @empty
                                        <option value="">No income available</option>
                                    @endforelse
                                </select>
                                <div class="form-text">Select which income to save from</div>
                            </div>
                            <div class="mb-2">
                                <label for="deposit_amount" class="form-label">Amount</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" name="amount" id="deposit_amount" 
                                           class="form-control" min="0.01" step="0.01" required>
                                </div>
                            </div>
                            <div class="mb-2">
                                <label for="deposit_date" class="form-label">Date</label>
                                <input type="date" name="transaction_date" id="deposit_date" 
                                       class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="deposit_notes" class="form-label">Notes</label>
                                <input type="text" name="notes" id="deposit_notes" 
                                       class="form-control" placeholder="Optional notes">
                            </div>
                            <button type="submit" class="btn btn-success w-100">
                                <i class="fas fa-plus me-1"></i> Add Deposit
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Withdraw Form -->
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent">
                        <h5 class="mb-0 text-danger"><i class="fas fa-minus-circle me-1"></i> Withdraw</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('savings.withdraw', $savings->id) }}">
                            @csrf
                            <div class="mb-2">
                                <label for="withdraw_amount" class="form-label">Amount</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" name="amount" id="withdraw_amount" 
                                           class="form-control" min="0.01" step="0.01" 
                                           max="{{ $savings->current_balance }}" required>
                                </div>
                                <div class="form-text">Available: ${{ number_format($savings->current_balance, 2) }}</div>
                            </div>
                            <div class="mb-2">
                                <label for="withdraw_date" class="form-label">Date</label>
                                <input type="date" name="transaction_date" id="withdraw_date" 
                                       class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="withdraw_notes" class="form-label">Notes</label>
                                <input type="text" name="notes" id="withdraw_notes" 
                                       class="form-control" placeholder="Optional notes">
                            </div>
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="fas fa-minus me-1"></i> Withdraw
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Transaction History -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Transaction History</h5>
                <span class="badge bg-secondary">{{ $savings->transactions->count() }} transactions</span>
            </div>
            <div class="card-body p-0">
                @if($savings->transactions->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Source</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($savings->transactions as $transaction)
                            <tr>
                                <td>{{ $transaction->transaction_date->format('M d, Y') }}</td>
                                <td>
                                    <span class="badge bg-{{ $transaction->type == 'deposit' ? 'success' : 'danger' }}">
                                        {{ ucfirst($transaction->type) }}
                                    </span>
                                </td>
                                <td class="fw-semibold {{ $transaction->type == 'deposit' ? 'text-success' : 'text-danger' }}">
                                    {{ $transaction->type == 'deposit' ? '+' : '-' }}${{ number_format($transaction->amount, 2) }}
                                </td>
                                <td>
                                    @if($transaction->income)
                                        <span class="text-muted">{{ $transaction->income->category->name ?? 'Income' }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>{{ $transaction->notes ?? '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-5">
                    <div class="mb-3">
                        <i class="fas fa-history fa-4x text-muted"></i>
                    </div>
                    <h5>No transactions yet</h5>
                    <p class="text-muted mb-0">Make your first deposit from your income to start saving!</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
