@extends('layouts.portal')

@section('title', 'Create Savings Account - FinTrack')

@section('content')
<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">Create Savings Account</h4>
        <p class="text-muted mb-0">Set up a new savings goal or account</p>
    </div>
    <a href="{{ route('savings.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i> Back to Savings
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form method="POST" action="{{ route('savings.store') }}">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Account Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" 
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name') }}" required>
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">e.g., Emergency Fund, Vacation, New Savings Car Fund</div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" id="description" rows="3"
                                  class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                        @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Optional notes about this savings account</div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="target_amount" class="form-label">Target Amount</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" name="target_amount" id="target_amount" 
                                       class="form-control @error('target_amount') is-invalid @enderror"
                                       value="{{ old('target_amount') }}" 
                                       min="0" step="0.01">
                                @error('target_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-text">Set a savings goal (optional)</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="current_balance" class="form-label">Initial Balance</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" name="current_balance" id="current_balance" 
                                       class="form-control @error('current_balance') is-invalid @enderror"
                                       value="{{ old('current_balance', 0) }}" 
                                       min="0" step="0.01">
                                @error('current_balance')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-text">Starting balance (optional)</div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                        <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                            <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="archived" {{ old('status') == 'archived' ? 'selected' : '' }}>Archived</option>
                        </select>
                        @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Create Savings Account
                        </button>
                        <a href="{{ route('savings.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent">
                <h5 class="mb-0">Tips for Saving</h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-3">
                        <div class="d-flex">
                            <div class="me-3 text-primary">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div>
                                <strong>Set realistic goals</strong>
                                <p class="text-muted mb-0 small">Break down large goals into smaller milestones</p>
                            </div>
                        </div>
                    </li>
                    <li class="mb-3">
                        <div class="d-flex">
                            <div class="me-3 text-primary">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div>
                                <strong>Track your progress</strong>
                                <p class="text-muted mb-0 small">Regularly update your savings balance</p>
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="d-flex">
                            <div class="me-3 text-primary">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div>
                                <strong>Stay consistent</strong>
                                <p class="text-muted mb-0 small">Make deposits a regular habit</p>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
