@extends('layouts.portal')

@section('title', 'Quote Details - FinTrack')

@section('content')
<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">Quote Details</h4>
        <p class="text-muted mb-0">Quote #{{ $quote->quote_number }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('quotes.pdf', $quote->id) }}" class="btn btn-danger">
            <i class="fas fa-file-pdf me-1"></i> Download PDF
        </a>
        <a href="{{ route('quotes.edit', $quote->id) }}" class="btn btn-outline-secondary">
            <i class="fas fa-edit me-1"></i> Edit
        </a>
        <a href="{{ route('quotes.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
    </div>
</div>

<!-- Status Alert -->
@if($quote->status == 'expired')
<div class="alert alert-warning">
    <i class="fas fa-exclamation-triangle me-2"></i> This quote has expired on {{ $quote->valid_until->format('M d, Y') }}.
</div>
@elseif($quote->status == 'accepted')
<div class="alert alert-success">
    <i class="fas fa-check-circle me-2"></i> This quote has been accepted.
</div>
@elseif($quote->status == 'rejected')
<div class="alert alert-danger">
    <i class="fas fa-times-circle me-2"></i> This quote has been rejected.
</div>
@elseif($quote->status == 'converted')
<div class="alert alert-info">
    <i class="fas fa-file-invoice me-2"></i> This quote has been converted to an invoice.
</div>
@endif

<div class="row">
    <div class="col-lg-8">
        <!-- Quote Information -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Quote Information</h5>
                @php
                    $statusClass = match($quote->status) {
                        'draft' => 'bg-secondary',
                        'sent' => 'bg-info',
                        'accepted' => 'bg-success',
                        'rejected' => 'bg-danger',
                        'expired' => 'bg-warning',
                        'converted' => 'bg-primary',
                        default => 'bg-secondary'
                    };
                @endphp
                <span class="badge {{ $statusClass }}">{{ ucfirst($quote->status) }}</span>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="text-muted small">Client</label>
                        <p class="mb-0 fw-semibold">
                            @if($quote->client)
                                <a href="{{ route('clients.show', $quote->client->id) }}" class="text-decoration-none">
                                    {{ $quote->client->name }}
                                </a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small">Project</label>
                        <p class="mb-0">
                            @if($quote->project)
                                <a href="{{ route('projects.show', $quote->project->id) }}" class="text-decoration-none">
                                    {{ $quote->project->title }}
                                </a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small">Quote Number</label>
                        <p class="mb-0 fw-semibold">{{ $quote->quote_number }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small">Issue Date</label>
                        <p class="mb-0">{{ $quote->issue_date->format('M d, Y') }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small">Valid Until</label>
                        <p class="mb-0 {{ $quote->valid_until->isPast() && $quote->status !== 'accepted' && $quote->status !== 'converted' ? 'text-danger fw-semibold' : '' }}">
                            {{ $quote->valid_until->format('M d, Y') }}
                        </p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small">Created At</label>
                        <p class="mb-0">{{ $quote->created_at->format('M d, Y h:i A') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quote Items -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">Quote Items</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Description</th>
                                <th class="text-center">Quantity</th>
                                <th class="text-end">Unit Price</th>
                                <th class="text-end pe-4">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($quote->items as $item)
                            <tr>
                                <td class="ps-4">{{ $item->description }}</td>
                                <td class="text-center">{{ number_format($item->quantity, 2) }}</td>
                                <td class="text-end">{{ $currencySymbol }}{{ number_format($item->unit_price, 2) }}</td>
                                <td class="text-end pe-4 fw-semibold">{{ $currencySymbol }}{{ number_format($item->total, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Notes -->
        @if($quote->notes)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">Notes</h5>
            </div>
            <div class="card-body">
                <p class="mb-0">{{ $quote->notes }}</p>
            </div>
        </div>
        @endif
    </div>

    <!-- Summary Sidebar -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">Summary</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Subtotal:</span>
                    <span class="fw-semibold">{{ $currencySymbol }}{{ number_format($quote->subtotal, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Tax:</span>
                    <span class="fw-semibold">{{ $currencySymbol }}{{ number_format($quote->tax_amount, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Discount:</span>
                    <span class="fw-semibold">-{{ $currencySymbol }}{{ number_format($quote->discount_amount, 2) }}</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                    <span class="fw-bold">Total:</span>
                    <span class="fw-bold text-primary fs-5">{{ $currencySymbol }}{{ number_format($quote->total_amount, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('quotes.pdf', $quote->id) }}" class="btn btn-danger">
                        <i class="fas fa-file-pdf me-2"></i> Download PDF
                    </a>
                    @if($quote->status == 'draft')
                    <form action="{{ route('quotes.update', $quote->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="client_id" value="{{ $quote->client_id }}">
                        <input type="hidden" name="project_id" value="{{ $quote->project_id }}">
                        <input type="hidden" name="issue_date" value="{{ $quote->issue_date->format('Y-m-d') }}">
                        <input type="hidden" name="valid_until" value="{{ $quote->valid_until->format('Y-m-d') }}">
                        <input type="hidden" name="subtotal" value="{{ $quote->subtotal }}">
                        <input type="hidden" name="tax_amount" value="{{ $quote->tax_amount }}">
                        <input type="hidden" name="discount_amount" value="{{ $quote->discount_amount }}">
                        <input type="hidden" name="total_amount" value="{{ $quote->total_amount }}">
                        <input type="hidden" name="status" value="sent">
                        <input type="hidden" name="notes" value="{{ $quote->notes }}">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-paper-plane me-2"></i> Mark as Sent
                        </button>
                    </form>
                    @endif
                    @if($quote->status == 'sent')
                    <form action="{{ route('quotes.update', $quote->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="client_id" value="{{ $quote->client_id }}">
                        <input type="hidden" name="project_id" value="{{ $quote->project_id }}">
                        <input type="hidden" name="issue_date" value="{{ $quote->issue_date->format('Y-m-d') }}">
                        <input type="hidden" name="valid_until" value="{{ $quote->valid_until->format('Y-m-d') }}">
                        <input type="hidden" name="subtotal" value="{{ $quote->subtotal }}">
                        <input type="hidden" name="tax_amount" value="{{ $quote->tax_amount }}">
                        <input type="hidden" name="discount_amount" value="{{ $quote->discount_amount }}">
                        <input type="hidden" name="total_amount" value="{{ $quote->total_amount }}">
                        <input type="hidden" name="status" value="accepted">
                        <input type="hidden" name="notes" value="{{ $quote->notes }}">
                        <button type="submit" class="btn btn-success w-100 mb-2">
                            <i class="fas fa-check me-2"></i> Mark as Accepted
                        </button>
                    </form>
                    <form action="{{ route('quotes.update', $quote->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="client_id" value="{{ $quote->client_id }}">
                        <input type="hidden" name="project_id" value="{{ $quote->project_id }}">
                        <input type="hidden" name="issue_date" value="{{ $quote->issue_date->format('Y-m-d') }}">
                        <input type="hidden" name="valid_until" value="{{ $quote->valid_until->format('Y-m-d') }}">
                        <input type="hidden" name="subtotal" value="{{ $quote->subtotal }}">
                        <input type="hidden" name="tax_amount" value="{{ $quote->tax_amount }}">
                        <input type="hidden" name="discount_amount" value="{{ $quote->discount_amount }}">
                        <input type="hidden" name="total_amount" value="{{ $quote->total_amount }}">
                        <input type="hidden" name="status" value="rejected">
                        <input type="hidden" name="notes" value="{{ $quote->notes }}">
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="fas fa-times me-2"></i> Mark as Rejected
                        </button>
                    </form>
                    @endif
                    @if($quote->status == 'accepted' && !$quote->invoices->count())
                    <a href="{{ route('invoices.create') }}?quote_id={{ $quote->id }}" class="btn btn-outline-primary">
                        <i class="fas fa-file-invoice me-2"></i> Convert to Invoice
                    </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Danger Zone -->
        <div class="card border-0 shadow-sm mt-4 border-danger">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 text-danger">Danger Zone</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('quotes.destroy', $quote->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this quote? This action cannot be undone.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger w-100">
                        <i class="fas fa-trash me-2"></i> Delete Quote
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
