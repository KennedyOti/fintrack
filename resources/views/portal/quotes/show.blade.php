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
        <button type="button" class="btn btn-outline-primary" onclick="openShareModal()"
                title="Generate a shareable client link">
            <i class="fas fa-share-nodes me-1"></i> Share
        </button>
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
<div class="alert alert-info d-flex align-items-center justify-content-between">
    <span><i class="fas fa-file-invoice me-2"></i> This quote has been converted to an invoice.</span>
    @if($quote->invoices->count())
    <a href="{{ route('invoices.show', $quote->invoices->first()) }}" class="btn btn-sm btn-info text-white ms-3">
        <i class="fas fa-external-link-alt me-1"></i> View Invoice
    </a>
    @endif
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
                    <form action="{{ route('quotes.status', $quote->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="sent">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-paper-plane me-2"></i> Mark as Sent
                        </button>
                    </form>
                    @endif
                    @if($quote->status == 'sent')
                    <form action="{{ route('quotes.status', $quote->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="accepted">
                        <button type="submit" class="btn btn-success w-100 mb-2">
                            <i class="fas fa-check me-2"></i> Mark as Accepted
                        </button>
                    </form>
                    <form action="{{ route('quotes.status', $quote->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="rejected">
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="fas fa-times me-2"></i> Mark as Rejected
                        </button>
                    </form>
                    @endif
                    @if($quote->status == 'accepted' && !$quote->invoices->count())
                    <a href="{{ route('quotes.convert', $quote) }}" class="btn btn-success w-100">
                        <i class="fas fa-file-invoice me-2"></i> Convert to Invoice
                    </a>
                    @endif
                    @if($quote->status == 'converted' && $quote->invoices->count())
                    <a href="{{ route('invoices.show', $quote->invoices->first()) }}" class="btn btn-outline-primary w-100">
                        <i class="fas fa-file-invoice me-2"></i> View Invoice
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
                        <h5 class="modal-title mb-0 fw-bold" id="shareModalLabel">Share Quote with Client</h5>
                        <p class="text-muted mb-0" style="font-size:12px;">Client can view, download &amp; respond to the quote</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            {{-- Body --}}
            <div class="modal-body px-4 py-3">
                {{-- No link yet --}}
                <div id="share-no-link" class="{{ $quote->share_token ? 'd-none' : '' }}">
                    <div class="text-center py-3">
                        <div style="width:64px;height:64px;background:#F1F5F9;border-radius:16px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                            <i class="fas fa-link text-muted fa-xl"></i>
                        </div>
                        <p class="text-muted mb-3" style="font-size:13.5px;">
                            Create a secure link for your client to review this quote — no login needed.
                        </p>
                        <ul class="text-start list-unstyled text-muted mb-4" style="font-size:13px; display:inline-block;">
                            <li class="mb-1"><i class="fas fa-check text-success me-2"></i>Client can view the full quote</li>
                            <li class="mb-1"><i class="fas fa-check text-success me-2"></i>Client can accept or reject inline</li>
                            <li class="mb-1"><i class="fas fa-check text-success me-2"></i>Client can download a PDF copy</li>
                            <li><i class="fas fa-check text-success me-2"></i>Revoke access anytime</li>
                        </ul>
                        <button class="btn btn-primary px-4" onclick="generateShareLink()" id="generate-btn">
                            <i class="fas fa-magic-wand-sparkles me-2"></i>Generate Share Link
                        </button>
                    </div>
                </div>

                {{-- Link exists --}}
                <div id="share-has-link" class="{{ $quote->share_token ? '' : 'd-none' }}">
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:12.5px;">Client Link</label>
                        <div class="input-group">
                            <input type="text" class="form-control form-control-sm" id="share-url-input"
                                   value="{{ $quote->getShareUrl() ?? '' }}" readonly
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
                        <a href="{{ $quote->getShareUrl() ?? '#' }}" id="share-preview-link"
                           class="btn btn-sm btn-outline-primary" target="_blank">
                            <i class="fas fa-eye me-1"></i>Preview as Client
                        </a>
                    </div>

                    <hr class="my-3">

                    <div class="d-flex justify-content-between align-items-center">
                        <button class="btn btn-sm btn-outline-secondary" onclick="regenerateLink()"
                                title="Regenerate will invalidate the current link">
                            <i class="fas fa-rotate me-1"></i>Regenerate Link
                        </button>
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
const QUOTE_ID = {{ $quote->id }};
const CSRF_TOKEN = '{{ csrf_token() }}';

function openShareModal() {
    const modal = new bootstrap.Modal(document.getElementById('shareModal'));
    modal.show();
}

function setLoading(on) {
    document.getElementById('share-loading').classList.toggle('d-none', !on);
    document.getElementById('share-no-link').classList.add('d-none');
    document.getElementById('share-has-link').classList.add('d-none');
}

function generateShareLink() {
    setLoading(true);
    fetch(`/quotes/${QUOTE_ID}/share`, {
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
    fetch(`/quotes/${QUOTE_ID}/share`, {
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
