@extends('layouts.portal')

@php
    $isReceivable = request()->routeIs('debts.receivable.*');
    $type         = $isReceivable ? 'receivable' : 'payable';
    $items        = $isReceivable ? ($receivables ?? collect()) : ($payables ?? collect());
@endphp

@section('title', ($isReceivable ? 'Receivables' : 'Payables') . ' - FinTrack')

@section('styles')
<style>
.debt-tabs .nav-link {
    border-radius:10px 10px 0 0; font-weight:500; padding:.6rem 1.5rem;
    color:#6c757d; border:1px solid transparent; transition:all .2s;
}
.debt-tabs .nav-link.active { background:#fff; border-color:#dee2e6 #dee2e6 #fff; color:#0d6efd; }
.stat-card { border-radius:14px; border:none; transition:transform .15s,box-shadow .15s; }
.stat-card:hover { transform:translateY(-3px); box-shadow:0 8px 24px rgba(0,0,0,.09)!important; }
.stat-icon { width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.2rem; }
.progress-thin { height:6px;border-radius:3px; }
.debt-row td { vertical-align:middle; }
.debt-row:hover { background-color:#f8f9ff!important; }
.overdue-dot { width:7px;height:7px;border-radius:50%;background:#dc3545;display:inline-block;margin-right:5px;animation:blink 1.4s infinite; }
@keyframes blink { 0%,100%{opacity:1} 50%{opacity:.3} }
.status-badge { font-size:.72rem;padding:.3rem .7rem;border-radius:20px;font-weight:600;letter-spacing:.3px; }
.empty-state { padding:4.5rem 2rem; }
.empty-icon-wrap { width:88px;height:88px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;margin-bottom:1.2rem; }
</style>
@endsection

@section('content')

{{-- Page Header --}}
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h4 class="mb-1">
            @if($isReceivable)
                <i class="fas fa-hand-holding-usd text-success me-2"></i>Debts Receivable
            @else
                <i class="fas fa-file-invoice-dollar text-danger me-2"></i>Debts Payable
            @endif
        </h4>
        <p class="text-muted mb-0 small">
            @if($isReceivable)
                Track money owed <strong>to you</strong> by clients
            @else
                Track money you <strong>owe</strong> to vendors &amp; creditors
            @endif
        </p>
    </div>
    <a href="{{ $isReceivable ? route('debts.receivable.create') : route('debts.payable.create') }}"
       class="btn btn-{{ $isReceivable ? 'success' : 'danger' }}">
        <i class="fas fa-plus me-1"></i> Add {{ $isReceivable ? 'Receivable' : 'Payable' }}
    </a>
</div>

{{-- Type Tabs --}}
<ul class="nav debt-tabs mb-0 border-bottom">
    <li class="nav-item">
        <a class="nav-link {{ $isReceivable ? 'active' : '' }}" href="{{ route('debts.receivable.index') }}">
            <i class="fas fa-hand-holding-usd me-1 text-success"></i> Receivables
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ !$isReceivable ? 'active' : '' }}" href="{{ route('debts.payable.index') }}">
            <i class="fas fa-file-invoice-dollar me-1 text-danger"></i> Payables
        </a>
    </li>
</ul>

{{-- Summary Cards --}}
@if($items->count() > 0)
@php
    $allItems     = $items->getCollection();
    $totalOrig    = $allItems->sum('original_amount');
    $totalPaid    = $allItems->sum('paid_amount');
    $totalOutst   = $allItems->sum(fn($d) => $d->original_amount - $d->paid_amount);
    $overdueNum   = $allItems->filter(fn($d) => \Carbon\Carbon::parse($d->due_date)->isPast() && $d->status !== 'paid')->count();
    $paidNum      = $allItems->filter(fn($d) => $d->status === 'paid')->count();
    $pctCollected = $totalOrig > 0 ? min(100, round(($totalPaid / $totalOrig) * 100)) : 0;
    $color        = $isReceivable ? 'success' : 'danger';
@endphp
<div class="row g-3 mt-2 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card shadow-sm p-3">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary"><i class="fas fa-layer-group"></i></div>
                <div>
                    <div class="text-muted small">Total Records</div>
                    <div class="fw-bold fs-4">{{ $items->total() }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card shadow-sm p-3">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon bg-{{ $color }} bg-opacity-10 text-{{ $color }}"><i class="fas fa-coins"></i></div>
                <div>
                    <div class="text-muted small">Total Amount</div>
                    <div class="fw-bold fs-5">{{ number_format($totalOrig, 2) }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card shadow-sm p-3">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon bg-warning bg-opacity-10 text-warning"><i class="fas fa-hourglass-half"></i></div>
                <div>
                    <div class="text-muted small">Outstanding</div>
                    <div class="fw-bold fs-5">{{ number_format($totalOutst, 2) }}</div>
                </div>
            </div>
            <div class="progress progress-thin mt-2">
                <div class="progress-bar bg-{{ $color }}" style="width:{{ $pctCollected }}%"></div>
            </div>
            <div class="d-flex justify-content-between mt-1">
                <small class="text-muted">{{ $pctCollected }}% settled</small>
                <small class="text-muted">{{ $paidNum }} paid</small>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card shadow-sm p-3">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon bg-danger bg-opacity-10 text-danger"><i class="fas fa-exclamation-triangle"></i></div>
                <div>
                    <div class="text-muted small">Overdue</div>
                    <div class="fw-bold fs-4 {{ $overdueNum > 0 ? 'text-danger' : 'text-muted' }}">{{ $overdueNum }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@else
<div class="mt-3"></div>
@endif

{{-- Filter Bar --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0 ps-0"
                           placeholder="{{ $isReceivable ? 'Search by client name…' : 'Search by vendor name…' }}"
                           value="{{ $search ?? '' }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>Partial</option>
                    <option value="paid"    {{ request('status') == 'paid'    ? 'selected' : '' }}>Paid</option>
                    <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search me-1"></i> Filter</button>
            </div>
            @if(request('search') || request('status'))
            <div class="col-md-2">
                <a href="{{ $isReceivable ? route('debts.receivable.index') : route('debts.payable.index') }}"
                   class="btn btn-outline-secondary w-100"><i class="fas fa-times me-1"></i> Clear</a>
            </div>
            @endif
        </form>
    </div>
</div>

{{-- Table Card --}}
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        @if($items->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light border-bottom">
                    <tr>
                        <th class="ps-4 py-3">{{ $isReceivable ? 'Client' : 'Vendor' }}</th>
                        @if($isReceivable)<th class="py-3">Invoice</th>@endif
                        <th class="py-3">Due Date</th>
                        <th class="py-3">Original</th>
                        <th class="py-3">Paid</th>
                        <th class="py-3">Outstanding</th>
                        <th class="py-3">Status</th>
                        <th class="py-3 text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $debt)
                    @php
                        $outstanding  = $debt->original_amount - $debt->paid_amount;
                        $isOverdueRow = \Carbon\Carbon::parse($debt->due_date)->isPast() && $debt->status !== 'paid';
                        $pct = $debt->original_amount > 0
                            ? min(100, round(($debt->paid_amount / $debt->original_amount) * 100)) : 0;
                    @endphp
                    <tr class="debt-row">
                        <td class="ps-4">
                            @if($isReceivable && $debt->client)
                                <div class="fw-semibold">
                                    @if($isOverdueRow)<span class="overdue-dot"></span>@endif
                                    <a href="{{ route('clients.show', $debt->client_id) }}"
                                       class="text-decoration-none text-dark">{{ $debt->client->name }}</a>
                                </div>
                                @if($debt->client->company_name)
                                    <small class="text-muted">{{ $debt->client->company_name }}</small>
                                @endif
                            @elseif(!$isReceivable)
                                <div class="fw-semibold">
                                    @if($isOverdueRow)<span class="overdue-dot"></span>@endif
                                    {{ $debt->vendor_name }}
                                </div>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        @if($isReceivable)
                        <td>
                            @if($debt->invoice)
                                <a href="{{ route('invoices.show', $debt->invoice_id) }}"
                                   class="badge bg-primary bg-opacity-10 text-primary text-decoration-none fw-semibold">
                                    {{ $debt->invoice->invoice_number }}
                                </a>
                            @else
                                <span class="badge bg-secondary bg-opacity-10 text-secondary">Manual</span>
                            @endif
                        </td>
                        @endif
                        <td>
                            <div class="{{ $isOverdueRow ? 'text-danger fw-semibold' : '' }}">
                                {{ \Carbon\Carbon::parse($debt->due_date)->format('M d, Y') }}
                            </div>
                            @if($isOverdueRow)
                                <small class="text-danger">
                                    <i class="fas fa-clock me-1"></i>{{ \Carbon\Carbon::parse($debt->due_date)->diffForHumans() }}
                                </small>
                            @endif
                        </td>
                        <td class="fw-semibold">{{ number_format($debt->original_amount, 2) }}</td>
                        <td>
                            <div class="text-success fw-semibold">{{ number_format($debt->paid_amount, 2) }}</div>
                            @if($pct > 0 && $pct < 100)
                                <div class="progress progress-thin mt-1" style="width:64px">
                                    <div class="progress-bar bg-success" style="width:{{ $pct }}%"></div>
                                </div>
                            @endif
                        </td>
                        <td>
                            <span class="fw-bold {{ $outstanding > 0 ? 'text-danger' : 'text-success' }}">
                                {{ number_format($outstanding, 2) }}
                            </span>
                        </td>
                        <td>
                            @switch($debt->status)
                                @case('pending')
                                    <span class="status-badge bg-warning-subtle text-warning border border-warning-subtle">
                                        <i class="fas fa-clock me-1"></i>Pending
                                    </span>@break
                                @case('partial')
                                    <span class="status-badge bg-info-subtle text-info border border-info-subtle">
                                        <i class="fas fa-adjust me-1"></i>Partial
                                    </span>@break
                                @case('paid')
                                    <span class="status-badge bg-success-subtle text-success border border-success-subtle">
                                        <i class="fas fa-check-circle me-1"></i>Paid
                                    </span>@break
                                @case('overdue')
                                    <span class="status-badge bg-danger-subtle text-danger border border-danger-subtle">
                                        <i class="fas fa-exclamation-circle me-1"></i>Overdue
                                    </span>@break
                            @endswitch
                        </td>
                        <td class="text-end pe-4">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light border" data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('debts.'.$type.'.show', $debt->id) }}">
                                            <i class="fas fa-eye me-2 text-primary"></i>View Details
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('debts.'.$type.'.edit', $debt->id) }}">
                                            <i class="fas fa-edit me-2 text-secondary"></i>Edit
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider my-1"></li>
                                    <li>
                                        <form action="{{ route('debts.'.$type.'.destroy', $debt->id) }}" method="POST">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger"
                                                    onclick="return confirm('Delete this record? This cannot be undone.')">
                                                <i class="fas fa-trash me-2"></i>Delete
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($items->hasPages())
        <div class="px-4 py-3 border-top">
            {{ $items->appends(request()->query())->links() }}
        </div>
        @endif
        @else
        <div class="empty-state text-center">
            @if($isReceivable)
                <div class="empty-icon-wrap bg-success bg-opacity-10 mx-auto">
                    <i class="fas fa-hand-holding-usd fa-2x text-success"></i>
                </div>
                <h5 class="fw-semibold">No receivables yet</h5>
                <p class="text-muted mb-4 mx-auto" style="max-width:380px">
                    Track money clients owe you. Receivables are also auto-created when you send an invoice.
                </p>
                <a href="{{ route('debts.receivable.create') }}" class="btn btn-success me-2">
                    <i class="fas fa-plus me-1"></i> Add Receivable
                </a>
                <a href="{{ route('invoices.create') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-file-invoice me-1"></i> Create Invoice
                </a>
            @else
                <div class="empty-icon-wrap bg-danger bg-opacity-10 mx-auto">
                    <i class="fas fa-file-invoice-dollar fa-2x text-danger"></i>
                </div>
                <h5 class="fw-semibold">No payables recorded</h5>
                <p class="text-muted mb-4 mx-auto" style="max-width:380px">
                    Keep track of everything you owe to vendors, suppliers, and creditors.
                </p>
                <a href="{{ route('debts.payable.create') }}" class="btn btn-danger">
                    <i class="fas fa-plus me-1"></i> Add Payable
                </a>
            @endif
        </div>
        @endif
    </div>
</div>

@endsection
