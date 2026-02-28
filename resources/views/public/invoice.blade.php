@extends('public.layout')

@section('title', 'Invoice ' . $invoice->invoice_number . ' — ' . ($businessInfo['business_name'] ?? 'FinTrack'))

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
            <div class="doc-type-label">Invoice</div>
            <div class="doc-number">#{{ $invoice->invoice_number }}</div>
            @php
                $statusClass = match($invoice->status) {
                    'draft'     => 'ds-draft',
                    'sent'      => 'ds-sent',
                    'partial'   => 'ds-partial',
                    'paid'      => 'ds-paid',
                    'overdue'   => 'ds-overdue',
                    'cancelled' => 'ds-cancelled',
                    default     => 'ds-draft',
                };
            @endphp
            <span class="doc-status {{ $statusClass }}">{{ ucfirst($invoice->status) }}</span>
        </div>
    </div>

    {{-- ── Document Body ── --}}
    <div class="doc-body">

        {{-- Info grid ──────────────────────────────────────── --}}
        <div class="info-grid">
            {{-- Bill To --}}
            <div class="info-block ib-client">
                <div class="info-block-label">Bill To</div>
                @if($invoice->client)
                    <div class="info-block-name">{{ $invoice->client->name }}</div>
                    <div class="info-block-detail">
                        @if($invoice->client->company_name) <div>{{ $invoice->client->company_name }}</div> @endif
                        @if($invoice->client->email)        <div>{{ $invoice->client->email }}</div>        @endif
                        @if($invoice->client->phone)        <div>{{ $invoice->client->phone }}</div>        @endif
                        @if($invoice->client->address)      <div>{{ $invoice->client->address }}</div>      @endif
                    </div>
                @else
                    <div class="info-block-detail text-muted">No client assigned</div>
                @endif
            </div>

            {{-- Invoice meta --}}
            <div class="info-block ib-meta">
                <div class="info-block-label">Invoice Details</div>
                <div class="meta-row">
                    <span class="ml">Invoice Number</span>
                    <span class="mv">{{ $invoice->invoice_number }}</span>
                </div>
                <div class="meta-row">
                    <span class="ml">Issue Date</span>
                    <span class="mv">{{ $invoice->issue_date->format('M d, Y') }}</span>
                </div>
                <div class="meta-row">
                    <span class="ml">Due Date</span>
                    <span class="mv {{ $invoice->isOverdue() ? 'overdue' : '' }}">
                        {{ $invoice->due_date->format('M d, Y') }}
                        @if($invoice->isOverdue()) <i class="fas fa-exclamation-triangle ms-1 fs-11"></i> @endif
                    </span>
                </div>
                @if($invoice->project)
                <div class="meta-row">
                    <span class="ml">Project</span>
                    <span class="mv">{{ $invoice->project->title }}</span>
                </div>
                @endif
                <div class="meta-row">
                    <span class="ml">Currency</span>
                    <span class="mv">{{ $currencyCode }}</span>
                </div>
            </div>
        </div>

        {{-- Overdue warning --}}
        @if($invoice->isOverdue())
        <div class="pub-alert pub-alert-error mb-4">
            <i class="fas fa-exclamation-triangle mt-1 flex-shrink-0"></i>
            <div>
                <strong>Payment Overdue</strong> — This invoice was due on {{ $invoice->due_date->format('M d, Y') }}.
                Please arrange payment as soon as possible.
            </div>
        </div>
        @endif

        {{-- Items ──────────────────────────────────────────── --}}
        <div class="items-section">
            <div class="section-heading">Line Items</div>
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
                        @forelse($invoice->items as $item)
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
                    <span class="sv">{{ $currencySymbol }}{{ number_format($invoice->subtotal, 2) }}</span>
                </div>
                @if($invoice->tax_amount > 0)
                <div class="sum-row">
                    <span class="sl">Tax</span>
                    <span class="sv">{{ $currencySymbol }}{{ number_format($invoice->tax_amount, 2) }}</span>
                </div>
                @endif
                @if($invoice->discount_amount > 0)
                <div class="sum-row">
                    <span class="sl">Discount</span>
                    <span class="sv" style="color: var(--ft-emerald);">
                        &minus;{{ $currencySymbol }}{{ number_format($invoice->discount_amount, 2) }}
                    </span>
                </div>
                @endif
                <div class="sum-row sum-divider sum-total">
                    <span class="sl">Total ({{ $currencyCode }})</span>
                    <span class="sv">{{ $currencySymbol }}{{ number_format($invoice->total_amount, 2) }}</span>
                </div>
                @if($invoice->paid_amount > 0)
                <div class="sum-row sum-paid">
                    <span class="sl">Amount Paid</span>
                    <span class="sv">{{ $currencySymbol }}{{ number_format($invoice->paid_amount, 2) }}</span>
                </div>
                @php $balance = $invoice->total_amount - $invoice->paid_amount; @endphp
                <div class="sum-row sum-balance {{ $balance <= 0 ? 'paid' : '' }}">
                    <span class="sl">Balance Due</span>
                    <span class="sv">{{ $currencySymbol }}{{ number_format(max($balance, 0), 2) }}</span>
                </div>
                @endif
            </div>
        </div>

        {{-- Notes ──────────────────────────────────────────── --}}
        @if($invoice->notes)
        <div class="notes-box">
            <div class="notes-label"><i class="fas fa-sticky-note me-1"></i> Notes</div>
            <p>{{ $invoice->notes }}</p>
        </div>
        @endif

    </div>{{-- /doc-body --}}

    {{-- ── Action Bar ── --}}
    <div class="action-bar">
        <a href="{{ route('public.invoice.pdf', $invoice->share_token) }}"
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
