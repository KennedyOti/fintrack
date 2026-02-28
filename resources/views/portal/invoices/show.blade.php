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
        <button type="button" class="btn btn-outline-primary" onclick="openShareModal()"
                title="Generate a shareable client link">
            <i class="fas fa-share-nodes me-1"></i> Share
        </button>
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
<div class="d-flex gap-2 flex-wrap">
    <a href="{{ route('invoices.pdf', ['invoice' => $invoice->id]) }}" class="btn btn-success" target="_blank">
        <i class="fas fa-file-pdf me-1"></i> Download PDF
    </a>
    <button type="button" class="btn btn-primary" onclick="openShareModal()">
        <i class="fas fa-share-nodes me-1"></i> Share with Client
    </button>
    <a href="{{ route('invoices.edit', ['invoice' => $invoice->id]) }}" class="btn btn-outline-secondary">
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

{{-- ══ Share Link Modal ══════════════════════════════════════════════════ --}}
<div class="modal fade" id="shareModal" tabindex="-1" aria-labelledby="shareModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            {{-- Header --}}
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:40px;height:40px;background:linear-gradient(135deg,#0E7490,#22D3EE);border-radius:10px;display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-share-nodes text-white"></i>
                    </div>
                    <div>
                        <h5 class="modal-title mb-0 fw-bold" id="shareModalLabel">Share Invoice with Client</h5>
                        <p class="text-muted mb-0" style="font-size:12px;">Generate a secure, unique link for your client</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            {{-- Body --}}
            <div class="modal-body px-4 py-3">
                {{-- No link yet --}}
                <div id="share-no-link" class="{{ $invoice->share_token ? 'd-none' : '' }}">
                    <div class="text-center py-3">
                        <div style="width:64px;height:64px;background:#F1F5F9;border-radius:16px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                            <i class="fas fa-link text-muted fa-xl"></i>
                        </div>
                        <p class="text-muted mb-3" style="font-size:13.5px;">
                            Create a secure, unique link for your client to view and download this invoice — no login required.
                        </p>
                        <ul class="text-start list-unstyled text-muted mb-4" style="font-size:13px; display:inline-block;">
                            <li class="mb-1"><i class="fas fa-check text-success me-2"></i>Client can view the invoice in their browser</li>
                            <li class="mb-1"><i class="fas fa-check text-success me-2"></i>Client can download a PDF copy</li>
                            <li class="mb-1"><i class="fas fa-check text-success me-2"></i>No account or login needed</li>
                            <li><i class="fas fa-check text-success me-2"></i>Revoke access anytime</li>
                        </ul>
                        <button class="btn btn-primary px-4" onclick="generateShareLink()" id="generate-btn">
                            <i class="fas fa-magic-wand-sparkles me-2"></i>Generate Share Link
                        </button>
                    </div>
                </div>

                {{-- Link exists --}}
                <div id="share-has-link" class="{{ $invoice->share_token ? '' : 'd-none' }}">
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:12.5px;">Client Link</label>
                        <div class="input-group">
                            <input type="text" class="form-control form-control-sm" id="share-url-input"
                                   value="{{ $invoice->getShareUrl() ?? '' }}" readonly
                                   style="font-size:12.5px; background:#F8FAFC;">
                            <button class="btn btn-outline-secondary btn-sm" type="button" onclick="copyShareUrl()" title="Copy to clipboard">
                                <i class="fas fa-copy" id="copy-icon"></i>
                            </button>
                        </div>
                        <div id="copy-feedback" class="text-success mt-1" style="font-size:12px; display:none;">
                            <i class="fas fa-check me-1"></i>Link copied to clipboard!
                        </div>
                    </div>

                    <div class="d-flex flex-column gap-2 mb-2">
                        <a href="{{ $invoice->getShareUrl() ?? '#' }}" id="share-preview-link"
                           class="btn btn-sm btn-outline-primary" target="_blank">
                            <i class="fas fa-eye me-1"></i>Preview as Client
                        </a>
                    </div>

                    <hr class="my-3">

                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <button class="btn btn-sm btn-outline-secondary" onclick="regenerateLink()"
                                    title="Regenerate will invalidate the current link">
                                <i class="fas fa-rotate me-1"></i>Regenerate Link
                            </button>
                        </div>
                        <button class="btn btn-sm btn-outline-danger" onclick="revokeLink()">
                            <i class="fas fa-ban me-1"></i>Revoke Access
                        </button>
                    </div>

                    <p class="text-muted mt-2 mb-0" style="font-size:11.5px;">
                        <i class="fas fa-info-circle me-1"></i>
                        Regenerating or revoking invalidates the current link instantly.
                    </p>
                </div>

                {{-- Loading state --}}
                <div id="share-loading" class="text-center py-4 d-none">
                    <div class="spinner-border text-primary" role="status" style="width:2rem;height:2rem;"></div>
                    <p class="text-muted mt-2 mb-0" style="font-size:13px;">Generating secure link…</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
const INVOICE_ID = {{ $invoice->id }};
const CSRF_TOKEN = '{{ csrf_token() }}';

function openShareModal() {
    const modal = new bootstrap.Modal(document.getElementById('shareModal'));
    modal.show();
}

function setLoading(on) {
    document.getElementById('share-loading').classList.toggle('d-none', !on);
    document.getElementById('share-no-link').classList.add('d-none');
    document.getElementById('share-has-link').classList.add('d-none');
    if (!on) {
        // will be restored by caller
    }
}

function generateShareLink() {
    setLoading(true);
    fetch(`/invoices/${INVOICE_ID}/share`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        document.getElementById('share-loading').classList.add('d-none');
        document.getElementById('share-url-input').value = data.url;
        document.getElementById('share-preview-link').href = data.url;
        document.getElementById('share-has-link').classList.remove('d-none');
    })
    .catch(() => {
        setLoading(false);
        document.getElementById('share-no-link').classList.remove('d-none');
        alert('Failed to generate link. Please try again.');
    });
}

function regenerateLink() {
    if (!confirm('Regenerate will invalidate the current link and create a new one. Continue?')) return;
    generateShareLink();
}

function revokeLink() {
    if (!confirm('Revoke access? The current link will stop working immediately.')) return;
    setLoading(true);
    fetch(`/invoices/${INVOICE_ID}/share`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(() => {
        document.getElementById('share-loading').classList.add('d-none');
        document.getElementById('share-no-link').classList.remove('d-none');
    })
    .catch(() => {
        setLoading(false);
        document.getElementById('share-has-link').classList.remove('d-none');
        alert('Failed to revoke link. Please try again.');
    });
}

function copyShareUrl() {
    const input = document.getElementById('share-url-input');
    input.select();
    navigator.clipboard.writeText(input.value).then(() => {
        document.getElementById('copy-icon').className = 'fas fa-check text-success';
        document.getElementById('copy-feedback').style.display = 'block';
        setTimeout(() => {
            document.getElementById('copy-icon').className = 'fas fa-copy';
            document.getElementById('copy-feedback').style.display = 'none';
        }, 2500);
    });
}
</script>
@endpush

@endsection
