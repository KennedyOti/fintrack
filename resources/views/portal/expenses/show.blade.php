@extends('layouts.portal')

@section('title', 'Expense Details - FinTrack')

@section('content')
<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">Expense Details</h4>
        <p class="text-muted mb-0">View expense information</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
        <a href="{{ route('expenses.edit', $expense->id) }}" class="btn btn-primary">
            <i class="fas fa-edit me-1"></i> Edit
        </a>
    </div>
</div>

<!-- Expense Details -->
<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">Expense Information</h5>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="text-muted small">Amount</div>
                        <h4 class="text-danger mb-0">{{ number_format($expense->amount, 2) }}</h4>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Expense Date</div>
                        <h5 class="mb-0">{{ $expense->expense_date->format('M d, Y') }}</h5>
                    </div>
                </div>
                
                <hr>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="text-muted small">Category</div>
                        @if($expense->category)
                        <span class="badge" style="background-color: {{ $expense->category->color ?? '#6c757d' }}; font-size: 14px;">
                            {{ $expense->category->name }}
                        </span>
                        @else
                        <span class="text-muted">-</span>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Funded From Income</div>
                        <span>{{ $expense->income ? number_format($expense->income->amount, 2) . ' - ' . $expense->income->income_date->format('M d, Y') : '-' }}</span>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="text-muted small">Vendor Name</div>
                        <span>{{ $expense->vendor_name ?: '-' }}</span>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Reference Number</div>
                        <span>{{ $expense->reference_number ?: '-' }}</span>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="text-muted small">Payment Method</div>
                        @switch($expense->payment_method)
                        @case('cash')
                        <span class="badge bg-info">Cash</span>
                        @break
                        @case('bank_transfer')
                        <span class="badge bg-primary">Bank Transfer</span>
                        @break
                        @case('mpesa')
                        <span class="badge bg-success">M-Pesa</span>
                        @break
                        @case('card')
                        <span class="badge bg-warning">Card</span>
                        @break
                        @default
                        <span class="badge bg-secondary">{{ ucfirst($expense->payment_method) }}</span>
                        @endswitch
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Created At</div>
                        <span>{{ $expense->created_at->format('M d, Y g:i A') }}</span>
                    </div>
                </div>
                
                @if($expense->notes)
                <div class="mt-3">
                    <div class="text-muted small">Notes</div>
                    <p class="mb-0">{{ $expense->notes }}</p>
                </div>
                @endif
            </div>
        </div>
        
        <!-- Delete Form -->
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="mb-3">Danger Zone</h6>
                <form action="{{ route('expenses.destroy', $expense->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this expense?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i> Delete Expense
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
