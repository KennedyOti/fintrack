@extends('layouts.portal')

@section('title', 'Edit Savings Account - FinTrack')

@section('content')
<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">Edit Savings Account</h4>
        <p class="text-muted mb-0">Update your savings account details</p>
    </div>
    <a href="{{ route('savings.show', ['saving' => $savings->id]) }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i> Back to Account
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form method="POST" action="{{ route('savings.update', ['saving' => $savings->id]) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Account Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" 
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $savings->name) }}" required>
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" id="description" rows="3"
                                  class="form-control @error('description') is-invalid @enderror">{{ old('description', $savings->description) }}</textarea>
                        @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="target_amount" class="form-label">Target Amount</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" name="target_amount" id="target_amount" 
                                       class="form-control @error('target_amount') is-invalid @enderror"
                                       value="{{ old('target_amount', $savings->target_amount) }}" 
                                       min="0" step="0.01">
                                @error('target_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="current_balance" class="form-label">Current Balance</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" name="current_balance" id="current_balance" 
                                       class="form-control bg-light" 
                                       value="{{ number_format($savings->current_balance, 2) }}" 
                                       disabled readonly>
                            </div>
                            <div class="form-text">Use deposit/withdraw to update balance</div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                        <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                            <option value="active" {{ old('status', $savings->status) == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="archived" {{ old('status', $savings->status) == 'archived' ? 'selected' : '' }}>Archived</option>
                        </select>
                        @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Update Savings Account
                        </button>
                        <a href="{{ route('savings.show', ['saving' => $savings->id]) }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent">
                <h5 class="mb-0">Account Summary</h5>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-6">Balance</dt>
                    <dd class="col-sm-6 fw-bold text-success">${{ number_format($savings->current_balance, 2) }}</dd>
                    
                    <dt class="col-sm-6">Target</dt>
                    <dd class="col-sm-6">{{ $savings->target_amount ? '$' . number_format($savings->target_amount, 2) : '-' }}</dd>
                    
                    <dt class="col-sm-6">Progress</dt>
                    <dd class="col-sm-6">{{ number_format($savings->progressPercentage(), 1) }}%</dd>
                    
                    <dt class="col-sm-6">Transactions</dt>
                    <dd class="col-sm-6">{{ $savings->transactions->count() }}</dd>
                </dl>
            </div>
        </div>

        <div class="card border-0 shadow-sm mt-3">
            <div class="card-header bg-transparent">
                <h5 class="mb-0">Danger Zone</h5>
            </div>
            <div class="card-body">
                <p class="text-muted small mb-3">Once you delete a savings account, there is no going back. Please be certain.</p>
                <form action="{{ route('savings.destroy', ['saving' => $savings->id]) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger w-100" 
                            onclick="return confirm('Are you sure you want to delete this savings account? This action cannot be undone.')">
                        <i class="fas fa-trash me-1"></i> Delete Account
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
