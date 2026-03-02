@extends('layouts.portal')

@section('title', 'Invoices - FinTrack')

@section('content')
<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">Invoices</h4>
        <p class="text-muted mb-0">Manage your invoices and track payments</p>
    </div>
    <a href="{{ route('invoices.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i> Create Invoice
    </a>
</div>

<!-- Filters -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" placeholder="Search by invoice number..." value="{{ $search }}">
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Sent</option>
                    <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>Partial</option>
                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="client_id" class="form-select">
                    <option value="">All Clients</option>
                    @foreach($clients as $client)
                        <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>
                            {{ $client->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search"></i>
                </button>
            </div>
            @if($search || request('status') || request('client_id'))
            <div class="col-md-2">
                <a href="{{ route('invoices.index') }}" class="btn btn-outline-secondary w-100">
                    <i class="fas fa-times me-1"></i> Clear
                </a>
            </div>
            @endif
        </form>
    </div>
</div>

<!-- Invoices Table -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        @if($invoices->count() > 0)
        <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Invoice #</th>
                        <th>Client</th>
                        <th>Issue Date</th>
                        <th>Due Date</th>
                        <th>Amount</th>
                        <th>Paid</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoices as $invoice)
                    <tr>
                        <td>
                            <a href="{{ route('invoices.show', $invoice->id) }}" class="text-decoration-none fw-semibold">
                                {{ $invoice->invoice_number }}
                            </a>
                        </td>
                        <td>
                            @if($invoice->client)
                                <a href="{{ route('clients.show', $invoice->client->id) }}" class="text-decoration-none">
                                    {{ $invoice->client->name }}
                                </a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>{{ $invoice->issue_date->format('M d, Y') }}</td>
                        <td>
                            <span class="{{ $invoice->isOverdue() ? 'text-danger fw-semibold' : '' }}">
                                {{ $invoice->due_date->format('M d, Y') }}
                            </span>
                        </td>
                        <td class="fw-semibold">{{ $currencySymbol }}{{ number_format($invoice->total_amount, 2) }}</td>
                        <td>
                            <span class="text-success">{{ $currencySymbol }}{{ number_format($invoice->paid_amount, 2) }}</span>
                        </td>
                        <td>
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
                        </td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light" data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('invoices.show', $invoice->id) }}">
                                            <i class="fas fa-eye me-2"></i>View
                                        </a>
                                    </li>
                                    @if($invoice->status != 'paid' && $invoice->status != 'cancelled')
                                    <li>
                                        <a class="dropdown-item text-success" href="{{ route('invoices.show', $invoice->id) }}#payment-form">
                                            <i class="fas fa-credit-card me-2"></i>Record Payment
                                        </a>
                                    </li>
                                    @endif
                                    <li>
                                        <a class="dropdown-item" href="{{ route('invoices.pdf', $invoice->id) }}" target="_blank">
                                            <i class="fas fa-file-pdf me-2"></i>Download PDF
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('invoices.edit', $invoice->id) }}">
                                            <i class="fas fa-edit me-2"></i>Edit
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('invoices.destroy', $invoice->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Are you sure you want to delete this invoice?')">
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
        @else
        <div class="text-center py-5">
            <i class="fas fa-file-invoice fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">No invoices found</h5>
            <p class="text-muted mb-3">Create your first invoice to get started</p>
            <a href="{{ route('invoices.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Create Invoice
            </a>
        </div>
        @endif
    </div>
</div>

<!-- Pagination -->
@if($invoices->hasPages())
<div class="mt-4">
    {{ $invoices->appends(request()->query())->links() }}
</div>
@endif
@endsection
