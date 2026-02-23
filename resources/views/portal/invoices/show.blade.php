@extends('layouts.portal')

@section('title', 'Invoice Details - FinTrack')

@section('content')
<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">Invoice #{{ $invoice->invoice_number }}</h4>
        <p class="text-muted mb-0">
            @switch($invoice->status)
                @case('draft')
                    <span class="badge bg-secondary">Draft</span>
                    @break
                @case('sent')
                    <span class="badge bg-info">Sent</span>
                    @break
                @case('partial')
                    <span class="badge bg-warning">Partial</span>
                    @break
                @case('paid')
                    <span class="badge bg-success">Paid</span>
                    @break
                @case('overdue')
                    <span class="badge bg-danger">Overdue</span>
                    @break
                @case('cancelled')
                    <span class="badge bg-dark">Cancelled</span>
                    @break
            @endswitch
            @if($invoice->isOverdue())
                <span class="badge bg-danger ms-1"><i class="fas fa-exclamation-triangle me-1"></i>Overdue</span>
            @endif
        </p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('invoices.edit', ['invoice' => $invoice->id]) }}" class="btn btn-outline-primary">
            <i class="fas fa-edit me-1"></i> Edit
        </a>
        <a href="{{ route('invoices.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
    </div>
</div>

@if($invoice->status != 'paid' && $invoice->status != 'cancelled')
<!-- Record Payment Form -->
<div class="card border-0 shadow-sm mb-4" id="payment-form">
    <div class="card-header bg-white py-3">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-credit-card me-2 text-success"></i>Record Payment</h5>
            <span class="badge bg-success-subtle text-success fs-6">
                Outstanding: {{ $currencySymbol }}{{ number_format($invoice->outstandingAmount(), 2) }}
            </span>
        </div>
    </div>
    <div class="card-body">
        <form action="{{ route('invoices.payment.store', ['invoice' => $invoice->id]) }}" method="POST" class="needs-validation" novalidate>
            @csrf
            <div class="row g-3">
                <div class="col-md-3">
                    <label for="amount" class="form-label fw-semibold">Amount <span class="text-danger">*</span></label>
                    <div class="input-group input-group-lg">
                        <span class="input-group-text bg-light">{{ $currencySymbol }}</span>
                        <input type="number" class="form-control" id="amount" name="amount" 
                               step="0.01" min="0.01" max="{{ $invoice->outstandingAmount() }}" 
                               value="{{ $invoice->outstandingAmount() }}" required>
                        <button type="button" class="btn btn-outline-success" onclick="document.getElementById('amount').value = '{{ $invoice->outstandingAmount() }}'" title="Pay Full Amount">
                            <i class="fas fa-wallet"></i>
                        </button>
                    </div>
                    <div class="form-text">Remaining balance: {{ $currencySymbol }}{{ number_format($invoice->outstandingAmount(), 2) }}</div>
                </div>
                <div class="col-md-3">
                    <label for="payment_date" class="form-label fw-semibold">Payment Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control form-control-lg" id="payment_date" name="payment_date" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="col-md-3">
                    <label for="payment_method" class="form-label fw-semibold">Payment Method <span class="text-danger">*</span></label>
                    <select class="form-select form-select-lg" id="payment_method" name="payment_method" required>
                        <option value="">Select method</option>
                        <option value="cash">💵 Cash</option>
                        <option value="bank_transfer">🏦 Bank Transfer</option>
                        <option value="mpesa">📱 M-Pesa</option>
                        <option value="credit_card">💳 Credit Card</option>
                        <option value="debit_card">💳 Debit Card</option>
                        <option value="cheque">📝 Cheque</option>
                        <option value="paypal">🅿️ PayPal</option>
                        <option value="other">📌 Other</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="reference_number" class="form-label fw-semibold">Reference Number</label>
                    <input type="text" class="form-control form-control-lg" id="reference_number" name="reference_number" placeholder="Transaction ID, Cheque #, etc.">
                </div>
                <div class="col-12">
                    <label for="notes" class="form-label fw-semibold">Notes</label>
                    <textarea class="form-control" id="notes" name="notes" rows="2" placeholder="Optional payment notes..."></textarea>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-success btn-lg px-5">
                        <i class="fas fa-check-circle me-2"></i> Record Payment
                    </button>
                    <span class="ms-3 text-muted">
                        <i class="fas fa-info-circle me-1"></i> This will create an income record and update the invoice status
                    </span>
                </div>
            </div>
        </form>
    </div>
</div>
@endif

<!-- Invoice Details -->
<div class="row g-4 mb-4">
    <!-- Invoice Info -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-file-invoice me-2 text-primary"></i>Invoice Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <p class="text-muted mb-1 small">Invoice Number</p>
                            <p class="mb-0 fw-semibold">{{ $invoice->invoice_number }}</p>
                        </div>
                        <div class="mb-3">
                            <p class="text-muted mb-1 small">Client</p>
                            <p class="mb-0">
                                @if($invoice->client)
                                    <a href="{{ route('clients.show', ['client' => $invoice->client->id]) }}" class="text-decoration-none">
                                        {{ $invoice->client->name }}
                                    </a>
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                        @if($invoice->project)
                        <div class="mb-3">
                            <p class="text-muted mb-1 small">Project</p>
                            <p class="mb-0">
                                <a href="{{ route('projects.show', ['project' => $invoice->project->id]) }}" class="text-decoration-none">
                                    {{ $invoice->project->title }}
                                </a>
                            </p>
                        </div>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <p class="text-muted mb-1 small">Issue Date</p>
                            <p class="mb-0">{{ $invoice->issue_date->format('M d, Y') }}</p>
                        </div>
                        <div class="mb-3">
                            <p class="text-muted mb-1 small">Due Date</p>
                            <p class="mb-0 {{ $invoice->isOverdue() ? 'text-danger fw-semibold' : '' }}">
                                {{ $invoice->due_date->format('M d, Y') }}
                            </p>
                        </div>
                        <div class="mb-0">
                            <p class="text-muted mb-1 small">Created</p>
                            <p class="mb-0">{{ $invoice->created_at->format('M d, Y g:i A') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Financial Summary -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-dollar-sign me-2 text-primary"></i>Financial Summary</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Subtotal:</span>
                    <span class="fw-semibold">{{ $currencySymbol }}{{ number_format($invoice->subtotal, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Tax:</span>
                    <span class="fw-semibold">{{ $currencySymbol }}{{ number_format($invoice->tax_amount, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Discount:</span>
                    <span class="fw-semibold">-{{ $currencySymbol }}{{ number_format($invoice->discount_amount, 2) }}</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between mb-3">
                    <span class="fw-bold">Total:</span>
                    <span class="fw-bold text-primary fs-5">{{ $currencySymbol }}{{ number_format($invoice->total_amount, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Paid:</span>
                    <span class="fw-semibold text-success">{{ $currencySymbol }}{{ number_format($actualPaidAmount ?? $invoice->paid_amount, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="fw-bold">Outstanding:</span>
                    <span class="fw-bold {{ ($invoice->total_amount - ($actualPaidAmount ?? $invoice->paid_amount)) > 0 ? 'text-danger' : 'text-success' }}">
                        {{ $currencySymbol }}{{ number_format($invoice->total_amount - ($actualPaidAmount ?? $invoice->paid_amount), 2) }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Invoice Items -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="fas fa-list me-2 text-primary"></i>Invoice Items</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Description</th>
                        <th>Quantity</th>
                        <th>Unit Price</th>
                        <th class="text-end pe-4">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoice->items as $item)
                    <tr>
                        <td class="ps-4">{{ $item->description }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ $currencySymbol }}{{ number_format($item->unit_price, 2) }}</td>
                        <td class="text-end pe-4 fw-semibold">{{ $currencySymbol }}{{ number_format($item->total, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-4 text-muted">No items found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Notes -->
@if($invoice->notes)
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="fas fa-sticky-note me-2 text-primary"></i>Notes</h5>
    </div>
    <div class="card-body">
        <p class="mb-0">{{ $invoice->notes }}</p>
    </div>
</div>
@endif

<!-- Payments -->
@if($invoice->payments->count() > 0)
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="fas fa-credit-card me-2 text-primary"></i>Payment History</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Date</th>
                        <th>Payment Method</th>
                        <th>Reference</th>
                        <th class="text-end pe-4">Amount</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->payments as $payment)
                    <tr>
                        <td class="ps-4">{{ $payment->payment_date->format('M d, Y') }}</td>
                        <td>{{ ucfirst($payment->payment_method) }}</td>
                        <td>{{ $payment->reference_number ?: '-' }}</td>
                        <td class="text-end pe-4 fw-semibold text-success">{{ $currencySymbol }}{{ number_format($payment->amount, 2) }}</td>
                        <td class="text-end">
                            <form action="{{ route('invoices.payment.destroy', ['invoice' => $invoice->id, 'payment' => $payment->id]) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this payment?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

<!-- Actions -->
<div class="d-flex gap-2">
    <a href="{{ route('invoices.pdf', ['invoice' => $invoice->id]) }}" class="btn btn-success" target="_blank">
        <i class="fas fa-file-pdf me-1"></i> Download PDF
    </a>
    <a href="{{ route('invoices.edit', ['invoice' => $invoice->id]) }}" class="btn btn-primary">
        <i class="fas fa-edit me-1"></i> Edit Invoice
    </a>
    <form action="{{ route('invoices.destroy', ['invoice' => $invoice->id]) }}" method="POST" class="d-inline">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Are you sure you want to delete this invoice?')">
            <i class="fas fa-trash me-1"></i> Delete Invoice
        </button>
    </form>
</div>
@endsection
