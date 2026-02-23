@extends('layouts.portal')

@section('title', 'Income Details - FinTrack')

@section('content')
<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">Income Details</h4>
        <p class="text-muted mb-0">View income information</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('income.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
        <a href="{{ route('income.edit', $income->id) }}" class="btn btn-primary">
            <i class="fas fa-edit me-1"></i> Edit
        </a>
    </div>
</div>

<!-- Income Details -->
<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">Income Information</h5>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="text-muted small">Amount</div>
                        <h4 class="text-success mb-0">{{ number_format($income->amount, 2) }}</h4>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Income Date</div>
                        <h5 class="mb-0">{{ $income->income_date->format('M d, Y') }}</h5>
                    </div>
                </div>
                
                <hr>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="text-muted small">Category</div>
                        @if($income->category)
                        <span class="badge" style="background-color: {{ $income->category->color ?? '#6c757d' }}; font-size: 14px;">
                            {{ $income->category->name }}
                        </span>
                        @else
                        <span class="text-muted">-</span>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Project</div>
                        <span>{{ $income->project ? $income->project->title : '-' }}</span>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="text-muted small">Client</div>
                        <span>{{ $income->client ? $income->client->name : '-' }}</span>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Reference Number</div>
                        <span>{{ $income->reference_number ?: '-' }}</span>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="text-muted small">Payment Method</div>
                        @switch($income->payment_method)
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
                        @case('paypal')
                        <span class="badge bg-purple">PayPal</span>
                        @break
                        @default
                        <span class="badge bg-secondary">{{ ucfirst($income->payment_method) }}</span>
                        @endswitch
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Created At</div>
                        <span>{{ $income->created_at->format('M d, Y g:i A') }}</span>
                    </div>
                </div>
                
                @if($income->notes)
                <div class="mt-3">
                    <div class="text-muted small">Notes</div>
                    <p class="mb-0">{{ $income->notes }}</p>
                </div>
                @endif
            </div>
        </div>
        
        <!-- Delete Form -->
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="mb-3">Danger Zone</h6>
                <form action="{{ route('income.destroy', $income->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this income?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i> Delete Income
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
