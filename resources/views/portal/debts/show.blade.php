@extends('layouts.portal')

@php
    $isReceivable = request()->routeIs('debts.receivable.*');
    $type         = $isReceivable ? 'receivable' : 'payable';
    $color        = $isReceivable ? 'success' : 'danger';
    $outstanding  = $debt->original_amount - $debt->paid_amount;
    $pct          = $debt->original_amount > 0 ? ($debt->paid_amount / $debt->original_amount) * 100 : 0;
    $isOverdue    = $debt->due_date->isPast() && $debt->status !== 'paid';

    $statusConfig = [
        'pending' => ['warning',  'clock',              'Pending'],
        'partial' => ['info',     'adjust',             'Partially Paid'],
        'paid'    => ['success',  'check-circle',       'Paid'],
        'overdue' => ['danger',   'exclamation-circle', 'Overdue'],
    ];
    $sc = $statusConfig[$debt->status] ?? ['secondary', 'circle', ucfirst($debt->status)];
@endphp

@section('title',
    ($isReceivable
        ? ($debt->client->name ?? 'Receivable')
        : $debt->vendor_name)
    . ' — ' . ($isReceivable ? 'Receivable' : 'Payable') . ' - FinTrack'
)

@section('styles')
<style>
.detail-card { border-radius:14px; border:none; }
.detail-card .card-header { border-radius:14px 14px 0 0; border-bottom:1px solid #f0f0f0; background:#fff; padding:1.1rem 1.5rem; }
.section-header { font-size:.78rem; font-weight:700; text-transform:uppercase; letter-spacing:.8px; color:#6c757d; }
.stat-box { border-radius:12px; padding:1.1rem 1.25rem; }
.detail-row { display:flex; justify-content:space-between; align-items:center; padding:.55rem 0; border-bottom:1px solid #f5f5f5; }
.detail-row:last-child { border-bottom:none; }
.detail-label { font-size:.82rem; color:#6c757d; font-weight:600; }
.detail-value { font-size:.92rem; color:#212529; font-weight:500; }
.payment-item { border-radius:10px; border:1px solid #f0f0f0; padding:.85rem 1rem; background:#fff; transition:box-shadow .15s; }
.payment-item:hover { box-shadow:0 2px 10px rgba(0,0,0,.07); }
.overdue-badge { animation: pulse-badge 1.8s ease-in-out infinite; }
@keyframes pulse-badge {
    0%, 100% { opacity:1; }
    50% { opacity:.65; }
}
.progress-sm { height:8px; border-radius:8px; }
.info-tag { font-size:.78rem; font-weight:600; text-transform:uppercase; letter-spacing:.6px; }
</style>
@endsection

@section('content')

{{-- Page Header --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item">
                    <a href="{{ route('debts.'.$type.'.index') }}" class="text-decoration-none text-muted">
                        {{ $isReceivable ? 'Receivables' : 'Payables' }}
                    </a>
                </li>
                <li class="breadcrumb-item active">
                    {{ $isReceivable ? ($debt->client->name ?? '—') : $debt->vendor_name }}
                </li>
            </ol>
        </nav>
        <h4 class="mb-0 d-flex align-items-center gap-2">
            <i class="fas fa-{{ $isReceivable ? 'hand-holding-usd text-success' : 'file-invoice-dollar text-danger' }}"></i>
            {{ $isReceivable ? ($debt->client->name ?? 'Unknown Client') : $debt->vendor_name }}
            <span class="badge bg-{{ $sc[0] }}-subtle text-{{ $sc[0] }} border border-{{ $sc[0] }}-subtle fs-6 {{ $debt->status === 'overdue' ? 'overdue-badge' : '' }}">
                <i class="fas fa-{{ $sc[1] }} me-1"></i>{{ $sc[2] }}
            </span>
        </h4>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('debts.'.$type.'.edit', $debt) }}" class="btn btn-{{ $color }}">
            <i class="fas fa-edit me-1"></i> Edit
        </a>
        <a href="{{ route('debts.'.$type.'.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
    </div>
</div>

{{-- Alert messages --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Top Summary Stats --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="stat-box bg-{{ $isReceivable ? 'success' : 'danger' }}-subtle border border-{{ $color }}-subtle">
            <div class="info-tag text-{{ $color }} mb-1">Total Amount</div>
            <div class="fs-4 fw-bold text-{{ $color }}">{{ number_format($debt->original_amount, 2) }}</div>
            <div class="text-muted small">{{ $isReceivable ? 'Owed to you' : 'You owe' }}</div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-box bg-success-subtle border border-success-subtle">
            <div class="info-tag text-success mb-1">Amount Paid</div>
            <div class="fs-4 fw-bold text-success">{{ number_format($debt->paid_amount, 2) }}</div>
            <div class="text-muted small">{{ number_format($pct, 1) }}% settled</div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-box {{ $outstanding > 0 ? 'bg-'.$color.'-subtle border border-'.$color.'-subtle' : 'bg-light border border-secondary-subtle' }}">
            <div class="info-tag text-{{ $outstanding > 0 ? $color : 'secondary' }} mb-1">Outstanding</div>
            <div class="fs-4 fw-bold text-{{ $outstanding > 0 ? $color : 'secondary' }}">{{ number_format($outstanding, 2) }}</div>
            <div class="text-muted small">{{ $outstanding <= 0 ? 'Fully settled' : 'Remaining balance' }}</div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-box {{ $isOverdue ? 'bg-danger-subtle border border-danger-subtle' : 'bg-light border border-secondary-subtle' }}">
            <div class="info-tag text-{{ $isOverdue ? 'danger' : 'secondary' }} mb-1">Due Date</div>
            <div class="fs-5 fw-bold text-{{ $isOverdue ? 'danger' : 'dark' }}">
                {{ $debt->due_date->format('M d, Y') }}
            </div>
            <div class="small {{ $isOverdue ? 'text-danger fw-semibold' : 'text-muted' }}">
                @if($debt->status === 'paid')
                    <i class="fas fa-check me-1 text-success"></i>Settled
                @elseif($isOverdue)
                    <i class="fas fa-exclamation-triangle me-1"></i>Overdue by {{ $debt->due_date->diffForHumans() }}
                @else
                    Due {{ $debt->due_date->diffForHumans() }}
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Progress Bar --}}
<div class="card border-0 shadow-sm mb-4 p-3">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <span class="text-muted small fw-semibold">Payment Progress</span>
        <span class="fw-bold text-{{ $color }}">{{ number_format($pct, 1) }}%</span>
    </div>
    <div class="progress progress-sm">
        <div class="progress-bar bg-{{ $color }}" role="progressbar"
             style="width: {{ min(100, $pct) }}%"
             aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100"></div>
    </div>
    <div class="d-flex justify-content-between mt-1">
        <small class="text-success">Paid: {{ number_format($debt->paid_amount, 2) }}</small>
        <small class="text-{{ $color }}">Remaining: {{ number_format($outstanding, 2) }}</small>
    </div>
</div>

<div class="row g-4">
    {{-- Main Details + Payment History --}}
    <div class="col-lg-8">

        {{-- Core Details --}}
        <div class="card detail-card shadow-sm mb-4">
            <div class="card-header">
                <span class="section-header">
                    <i class="fas fa-info-circle me-1"></i>
                    {{ $isReceivable ? 'Receivable' : 'Payable' }} Details
                </span>
            </div>
            <div class="card-body p-4">
                @if($isReceivable)
                    {{-- Client info --}}
                    <div class="detail-row">
                        <span class="detail-label"><i class="fas fa-user me-2 text-muted"></i>Client</span>
                        <span class="detail-value">
                            @if($debt->client)
                                <a href="{{ route('clients.show', $debt->client) }}" class="text-decoration-none fw-semibold">
                                    {{ $debt->client->name }}
                                </a>
                                @if($debt->client->company_name)
                                    <span class="text-muted small ms-1">({{ $debt->client->company_name }})</span>
                                @endif
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </span>
                    </div>
                    {{-- Linked Invoice --}}
                    <div class="detail-row">
                        <span class="detail-label"><i class="fas fa-file-invoice me-2 text-muted"></i>Linked Invoice</span>
                        <span class="detail-value">
                            @if($debt->invoice)
                                <a href="{{ route('invoices.show', $debt->invoice) }}" class="text-decoration-none fw-semibold text-primary">
                                    <i class="fas fa-external-link-alt me-1 small"></i>{{ $debt->invoice->invoice_number }}
                                </a>
                            @else
                                <span class="badge bg-light text-muted border">Manual Entry</span>
                            @endif
                        </span>
                    </div>
                @else
                    {{-- Vendor info --}}
                    <div class="detail-row">
                        <span class="detail-label"><i class="fas fa-building me-2 text-muted"></i>Vendor / Creditor</span>
                        <span class="detail-value fw-semibold">{{ $debt->vendor_name }}</span>
                    </div>
                @endif

                <div class="detail-row">
                    <span class="detail-label"><i class="fas fa-calendar-alt me-2 text-muted"></i>Due Date</span>
                    <span class="detail-value {{ $isOverdue ? 'text-danger fw-bold' : '' }}">
                        {{ $debt->due_date->format('F j, Y') }}
                        @if($isOverdue)
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle ms-2 small">Overdue</span>
                        @endif
                    </span>
                </div>

                <div class="detail-row">
                    <span class="detail-label"><i class="fas fa-circle-dot me-2 text-muted"></i>Status</span>
                    <span class="detail-value">
                        <span class="badge bg-{{ $sc[0] }}-subtle text-{{ $sc[0] }} border border-{{ $sc[0] }}-subtle px-3 py-2">
                            <i class="fas fa-{{ $sc[1] }} me-1"></i>{{ $sc[2] }}
                        </span>
                    </span>
                </div>

                <div class="detail-row">
                    <span class="detail-label"><i class="fas fa-clock me-2 text-muted"></i>Created</span>
                    <span class="detail-value text-muted">{{ $debt->created_at->format('M d, Y \a\t g:i A') }}</span>
                </div>

                @if($debt->updated_at->ne($debt->created_at))
                <div class="detail-row">
                    <span class="detail-label"><i class="fas fa-pen-to-square me-2 text-muted"></i>Last Updated</span>
                    <span class="detail-value text-muted">{{ $debt->updated_at->format('M d, Y \a\t g:i A') }}</span>
                </div>
                @endif

                @if($debt->notes)
                <div class="mt-3 pt-3 border-top">
                    <div class="detail-label mb-2"><i class="fas fa-sticky-note me-2 text-muted"></i>Notes</div>
                    <p class="mb-0 text-muted" style="white-space:pre-line;">{{ $debt->notes }}</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Payment History (Receivables only) --}}
        @if($isReceivable)
        <div class="card detail-card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="section-header">
                    <i class="fas fa-history me-1"></i> Payment History
                </span>
                <span class="badge bg-{{ $color }}-subtle text-{{ $color }} border border-{{ $color }}-subtle">
                    {{ $debt->payments->count() }} {{ Str::plural('payment', $debt->payments->count()) }}
                </span>
            </div>
            <div class="card-body p-4">
                @if($debt->payments->isEmpty())
                    <div class="text-center py-4">
                        <div class="mb-3">
                            <i class="fas fa-receipt fa-2x text-muted opacity-50"></i>
                        </div>
                        <p class="text-muted mb-1 fw-semibold">No payments recorded yet</p>
                        <p class="text-muted small">
                            Payments are recorded from the linked invoice or added manually.
                        </p>
                        @if($debt->invoice)
                        <a href="{{ route('invoices.show', $debt->invoice) }}" class="btn btn-sm btn-outline-success mt-2">
                            <i class="fas fa-file-invoice me-1"></i> View Invoice to Record Payment
                        </a>
                        @endif
                    </div>
                @else
                    <div class="d-flex flex-column gap-3">
                        @foreach($debt->payments->sortByDesc('payment_date') as $payment)
                        <div class="payment-item">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="d-flex gap-3 align-items-start">
                                    <div class="rounded-circle bg-success-subtle d-flex align-items-center justify-content-center flex-shrink-0"
                                         style="width:38px;height:38px;">
                                        <i class="fas fa-check text-success small"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold text-dark">
                                            {{ number_format($payment->amount, 2) }}
                                        </div>
                                        <div class="text-muted small mt-1">
                                            <i class="fas fa-calendar-day me-1"></i>
                                            {{ \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') }}
                                            @if($payment->payment_method)
                                                &nbsp;·&nbsp;
                                                <i class="fas fa-credit-card me-1"></i>
                                                {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}
                                            @endif
                                            @if($payment->reference_number)
                                                &nbsp;·&nbsp;
                                                <span class="text-muted">Ref: {{ $payment->reference_number }}</span>
                                            @endif
                                        </div>
                                        @if($payment->notes)
                                            <div class="text-muted small mt-1 fst-italic">{{ $payment->notes }}</div>
                                        @endif
                                    </div>
                                </div>
                                <span class="badge bg-success-subtle text-success border border-success-subtle">
                                    <i class="fas fa-check-circle me-1"></i>Received
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    {{-- Payments total --}}
                    <div class="mt-3 pt-3 border-top d-flex justify-content-between align-items-center">
                        <span class="text-muted small fw-semibold">Total Received</span>
                        <span class="fw-bold text-success fs-5">{{ number_format($debt->paid_amount, 2) }}</span>
                    </div>
                @endif
            </div>
        </div>
        @else
        {{-- Payable: note about payment tracking --}}
        <div class="card detail-card shadow-sm">
            <div class="card-header">
                <span class="section-header">
                    <i class="fas fa-info-circle me-1"></i> Payment Tracking
                </span>
            </div>
            <div class="card-body p-4 text-center py-5">
                <i class="fas fa-hand-holding-usd fa-2x text-danger opacity-50 mb-3"></i>
                <p class="text-muted fw-semibold mb-1">Update status to reflect payments</p>
                <p class="text-muted small">
                    For payables, mark this record as <strong>Partially Paid</strong> or <strong>Paid</strong>
                    and update the amount when you make payments.
                </p>
                <a href="{{ route('debts.payable.edit', $debt) }}" class="btn btn-sm btn-outline-danger mt-2">
                    <i class="fas fa-edit me-1"></i> Edit to Update Status
                </a>
            </div>
        </div>
        @endif

    </div>

    {{-- Sidebar --}}
    <div class="col-lg-4">

        {{-- Quick Actions --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <h6 class="fw-semibold mb-3">
                    <i class="fas fa-bolt me-2 text-{{ $color }}"></i>Quick Actions
                </h6>
                <div class="d-grid gap-2">
                    <a href="{{ route('debts.'.$type.'.edit', $debt) }}" class="btn btn-{{ $color }} btn-sm">
                        <i class="fas fa-edit me-2"></i>Edit {{ $isReceivable ? 'Receivable' : 'Payable' }}
                    </a>
                    @if($isReceivable && $debt->invoice)
                    <a href="{{ route('invoices.show', $debt->invoice) }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-file-invoice me-2"></i>View Linked Invoice
                    </a>
                    @endif
                    @if($isReceivable && $debt->client)
                    <a href="{{ route('clients.show', $debt->client) }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-user me-2"></i>View Client Profile
                    </a>
                    @endif
                    <a href="{{ route('debts.'.$type.'.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-list me-2"></i>All {{ $isReceivable ? 'Receivables' : 'Payables' }}
                    </a>
                </div>
            </div>
        </div>

        {{-- Financial Snapshot --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <h6 class="fw-semibold mb-3">
                    <i class="fas fa-balance-scale me-2 text-secondary"></i>Financial Snapshot
                </h6>
                <div class="mb-2 d-flex justify-content-between">
                    <span class="text-muted small">Original</span>
                    <span class="fw-semibold small">{{ number_format($debt->original_amount, 2) }}</span>
                </div>
                <div class="mb-2 d-flex justify-content-between">
                    <span class="text-muted small">Paid</span>
                    <span class="fw-semibold small text-success">{{ number_format($debt->paid_amount, 2) }}</span>
                </div>
                <div class="mb-3 d-flex justify-content-between">
                    <span class="text-muted small">Outstanding</span>
                    <span class="fw-semibold small text-{{ $outstanding > 0 ? $color : 'secondary' }}">
                        {{ number_format($outstanding, 2) }}
                    </span>
                </div>
                <div class="progress progress-sm">
                    <div class="progress-bar bg-{{ $color }}" style="width:{{ min(100,$pct) }}%"></div>
                </div>
                <div class="text-center text-muted small mt-2">{{ number_format($pct, 1) }}% complete</div>
            </div>
        </div>

        {{-- Danger Zone --}}
        <div class="card border-danger-subtle shadow-sm">
            <div class="card-body">
                <h6 class="fw-semibold text-danger mb-2">
                    <i class="fas fa-trash-alt me-2"></i>Danger Zone
                </h6>
                <p class="text-muted small mb-3">
                    Permanently remove this {{ $isReceivable ? 'receivable' : 'payable' }} record.
                    This action cannot be undone.
                </p>
                <form method="POST"
                      action="{{ $isReceivable ? route('debts.receivable.destroy', $debt) : route('debts.payable.destroy', $debt) }}"
                      onsubmit="return confirm('Delete this {{ $isReceivable ? 'receivable' : 'payable' }}? This cannot be undone.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                        <i class="fas fa-trash me-2"></i>Delete {{ $isReceivable ? 'Receivable' : 'Payable' }}
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>

@endsection
