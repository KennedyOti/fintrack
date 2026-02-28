@extends('public.layout')

@section('title', 'Quote ' . $quote->quote_number . ' — ' . ($businessInfo['business_name'] ?? 'FinTrack'))

@section('content')
<div class="doc-card">

    {{-- ── Document Header ── --}}
    <div class="doc-header">
        <div>
            <div class="doc-biz-name">{{ $businessInfo['business_name'] ?? 'Your Business' }}</div>
            <div class="doc-biz-detail">
                @if($businessInfo['business_address'])
                    <div>{!! nl2br(e($businessInfo['business_address'])) !!}</div>
                @endif
                @if($businessInfo['email'])    <div>{{ $businessInfo['email'] }}</div> @endif
                @if($businessInfo['phone'])    <div>{{ $businessInfo['phone'] }}</div> @endif
                @if($businessInfo['tax_number']) <div>Tax ID: {{ $businessInfo['tax_number'] }}</div> @endif
            </div>
        </div>
        <div class="doc-header-right">
            <div class="doc-type-label">Quote</div>
            <div class="doc-number">#{{ $quote->quote_number }}</div>
            @php
                $statusClass = match($quote->status) {
                    'draft'     => 'ds-draft',
                    'sent'      => 'ds-sent',
                    'accepted'  => 'ds-accepted',
                    'rejected'  => 'ds-rejected',
                    'expired'   => 'ds-expired',
                    'converted' => 'ds-converted',
                    default     => 'ds-draft',
                };
            @endphp
            <span class="doc-status {{ $statusClass }}">{{ ucfirst($quote->status) }}</span>
        </div>
    </div>

    {{-- ── Document Body ── --}}
    <div class="doc-body">

        {{-- Status banners ──────────────────────────────────── --}}
        @if($quote->status === 'accepted')
        <div class="quote-status-box qsb-accepted">
            <div class="qsb-icon text-success">
                <i class="fas fa-circle-check"></i>
            </div>
            <div class="qsb-text">
                <h6 class="text-success">Quote Accepted</h6>
                <p class="text-success" style="opacity:.75;">
                    You have accepted this quote. {{ $businessInfo['business_name'] ?? 'The business' }} will be in touch shortly to proceed.
                </p>
            </div>
        </div>

        @elseif($quote->status === 'rejected')
        <div class="quote-status-box qsb-rejected">
            <div class="qsb-icon text-danger">
                <i class="fas fa-circle-xmark"></i>
            </div>
            <div class="qsb-text">
                <h6 class="text-danger">Quote Rejected</h6>
                <p class="text-danger" style="opacity:.75;">
                    This quote has been declined. If you changed your mind, please contact {{ $businessInfo['business_name'] ?? 'us' }} directly.
                </p>
            </div>
        </div>

        @elseif($quote->status === 'expired')
        <div class="quote-status-box qsb-expired">
            <div class="qsb-icon text-warning">
                <i class="fas fa-clock"></i>
            </div>
            <div class="qsb-text">
                <h6 class="text-warning">Quote Expired</h6>
                <p style="color:#92400E; opacity:.75;">
                    This quote expired on {{ $quote->valid_until->format('M d, Y') }}. Please contact
                    {{ $businessInfo['business_name'] ?? 'us' }} for an updated quote.
                </p>
            </div>
        </div>

        @elseif($quote->status === 'converted')
        <div class="quote-status-box qsb-converted">
            <div class="qsb-icon text-primary">
                <i class="fas fa-file-invoice"></i>
            </div>
            <div class="qsb-text">
                <h6 class="text-primary">Converted to Invoice</h6>
                <p class="text-primary" style="opacity:.75;">
                    This quote was accepted and converted to an invoice.
                    Please check your invoice for payment details.
                </p>
            </div>
        </div>

        @elseif(in_array($quote->status, ['draft', 'sent']))
        {{-- Active quote — show the accept/reject call to action --}}
        <div class="quote-action-box">
            <h5><i class="fas fa-handshake me-2"></i>Review this Quote</h5>
            <p>
                Please review the details below. This quote is valid until
                <strong>{{ $quote->valid_until->format('M d, Y') }}</strong>.
                You can accept or reject it using the buttons below.
            </p>
            <div class="quote-action-btns">
                <form action="{{ route('public.quote.accept', $quote->share_token) }}" method="POST"
                      onsubmit="return confirm('Accept this quote from {{ addslashes($businessInfo['business_name'] ?? 'the business') }}?')">
                    @csrf
                    <button type="submit" class="btn btn-accept">
                        <i class="fas fa-check me-2"></i>Accept Quote
                    </button>
                </form>
                <form action="{{ route('public.quote.reject', $quote->share_token) }}" method="POST"
                      onsubmit="return confirm('Are you sure you want to reject this quote?')">
                    @csrf
                    <button type="submit" class="btn btn-reject">
                        <i class="fas fa-times me-2"></i>Reject Quote
                    </button>
                </form>
            </div>
        </div>
        @endif

        {{-- Info grid ──────────────────────────────────────── --}}
        <div class="info-grid">
            {{-- Client To --}}
            <div class="info-block ib-client">
                <div class="info-block-label">Prepared For</div>
                @if($quote->client)
                    <div class="info-block-name">{{ $quote->client->name }}</div>
                    <div class="info-block-detail">
                        @if($quote->client->company_name) <div>{{ $quote->client->company_name }}</div> @endif
                        @if($quote->client->email)        <div>{{ $quote->client->email }}</div>        @endif
                        @if($quote->client->phone)        <div>{{ $quote->client->phone }}</div>        @endif
                        @if($quote->client->address)      <div>{{ $quote->client->address }}</div>      @endif
                    </div>
                @else
                    <div class="info-block-detail text-muted">No client assigned</div>
                @endif
            </div>

            {{-- Quote meta --}}
            <div class="info-block ib-meta">
                <div class="info-block-label">Quote Details</div>
                <div class="meta-row">
                    <span class="ml">Quote Number</span>
                    <span class="mv">{{ $quote->quote_number }}</span>
                </div>
                <div class="meta-row">
                    <span class="ml">Issue Date</span>
                    <span class="mv">{{ $quote->issue_date->format('M d, Y') }}</span>
                </div>
                <div class="meta-row">
                    <span class="ml">Valid Until</span>
                    @php $isExpiredDate = $quote->valid_until->isPast() && !in_array($quote->status, ['accepted', 'converted']); @endphp
                    <span class="mv {{ $isExpiredDate ? 'overdue' : '' }}">
                        {{ $quote->valid_until->format('M d, Y') }}
                        @if($isExpiredDate) <i class="fas fa-exclamation-triangle ms-1"></i> @endif
                    </span>
                </div>
                @if($quote->project)
                <div class="meta-row">
                    <span class="ml">Project</span>
                    <span class="mv">{{ $quote->project->title }}</span>
                </div>
                @endif
                <div class="meta-row">
                    <span class="ml">Currency</span>
                    <span class="mv">{{ $currencyCode }}</span>
                </div>
            </div>
        </div>

        {{-- Items ──────────────────────────────────────────── --}}
        <div class="items-section">
            <div class="section-heading">Scope of Work</div>
            <div style="border-radius: 8px; overflow: hidden; border: 1px solid var(--border);">
                <table class="items-table">
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th>Qty</th>
                            <th>Unit Price</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($quote->items as $item)
                        <tr>
                            <td>{{ $item->description }}</td>
                            <td>{{ number_format($item->quantity, 2) }}</td>
                            <td>{{ $currencySymbol }}{{ number_format($item->unit_price, 2) }}</td>
                            <td>{{ $currencySymbol }}{{ number_format($item->total, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" style="text-align:center; padding: 24px; color: var(--text-muted);">
                                No items found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Summary ─────────────────────────────────────────── --}}
        <div class="summary-wrap">
            <div class="summary-box">
                <div class="sum-row">
                    <span class="sl">Subtotal</span>
                    <span class="sv">{{ $currencySymbol }}{{ number_format($quote->subtotal, 2) }}</span>
                </div>
                @if($quote->tax_amount > 0)
                <div class="sum-row">
                    <span class="sl">Tax</span>
                    <span class="sv">{{ $currencySymbol }}{{ number_format($quote->tax_amount, 2) }}</span>
                </div>
                @endif
                @if($quote->discount_amount > 0)
                <div class="sum-row">
                    <span class="sl">Discount</span>
                    <span class="sv" style="color: var(--ft-emerald);">
                        &minus;{{ $currencySymbol }}{{ number_format($quote->discount_amount, 2) }}
                    </span>
                </div>
                @endif
                <div class="sum-row sum-divider sum-total">
                    <span class="sl">Total ({{ $currencyCode }})</span>
                    <span class="sv">{{ $currencySymbol }}{{ number_format($quote->total_amount, 2) }}</span>
                </div>
            </div>
        </div>

        {{-- Notes ──────────────────────────────────────────── --}}
        @if($quote->notes)
        <div class="notes-box">
            <div class="notes-label"><i class="fas fa-sticky-note me-1"></i> Notes & Terms</div>
            <p>{{ $quote->notes }}</p>
        </div>
        @endif

        {{-- Bottom accept/reject (repeated for easy access after reading) --}}
        @if(in_array($quote->status, ['draft', 'sent']))
        <div style="text-align:center; padding: 8px 0 4px;">
            <p style="font-size:13px; color:var(--text-muted); margin-bottom:14px;">
                Ready to proceed? Accept or decline this quote below.
            </p>
            <div style="display:flex; gap:12px; justify-content:center; flex-wrap:wrap;">
                <form action="{{ route('public.quote.accept', $quote->share_token) }}" method="POST"
                      onsubmit="return confirm('Accept this quote from {{ addslashes($businessInfo['business_name'] ?? 'the business') }}?')">
                    @csrf
                    <button type="submit" class="btn btn-accept">
                        <i class="fas fa-check me-2"></i>Accept Quote
                    </button>
                </form>
                <form action="{{ route('public.quote.reject', $quote->share_token) }}" method="POST"
                      onsubmit="return confirm('Are you sure you want to reject this quote?')">
                    @csrf
                    <button type="submit" class="btn btn-reject">
                        <i class="fas fa-times me-2"></i>Reject Quote
                    </button>
                </form>
            </div>
        </div>
        @endif

    </div>{{-- /doc-body --}}

    {{-- ── Action Bar ── --}}
    <div class="action-bar">
        <a href="{{ route('public.quote.pdf', $quote->share_token) }}"
           class="btn btn-navy"
           target="_blank">
            <i class="fas fa-file-pdf me-2"></i>Download PDF
        </a>
        <span style="font-size:12px; color:var(--text-muted);">
            <i class="fas fa-lock me-1"></i>
            Secure link &mdash; this document is for your eyes only.
        </span>
    </div>

</div>{{-- /doc-card --}}
@endsection
