@extends('layouts.portal')

@section('title', 'Quotes - FinTrack')

@section('content')
<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">Quotes</h4>
        <p class="text-muted mb-0">Manage your quotes and send to clients</p>
    </div>
    <a href="{{ route('quotes.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i> Create Quote
    </a>
</div>

<!-- Filters -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" placeholder="Search by quote number..." value="{{ $search }}">
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Sent</option>
                    <option value="accepted" {{ request('status') == 'accepted' ? 'selected' : '' }}>Accepted</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                    <option value="converted" {{ request('status') == 'converted' ? 'selected' : '' }}>Converted</option>
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
                <a href="{{ route('quotes.index') }}" class="btn btn-outline-secondary w-100">
                    <i class="fas fa-times me-1"></i> Clear
                </a>
            </div>
            @endif
        </form>
    </div>
</div>

<!-- Quotes Table -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        @if($quotes->count() > 0)
        <div style="overflow: visible;">
            <table class="table table-hover mb-0" style="min-width: 100%;">
                <thead>
                    <tr>
                        <th>Quote #</th>
                        <th>Client</th>
                        <th>Issue Date</th>
                        <th>Valid Until</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($quotes as $quote)
                    <tr>
                        <td>
                            <a href="{{ route('quotes.show', $quote->id) }}" class="text-decoration-none fw-semibold">
                                {{ $quote->quote_number }}
                            </a>
                        </td>
                        <td>
                            @if($quote->client)
                                <a href="{{ route('clients.show', $quote->client->id) }}" class="text-decoration-none">
                                    {{ $quote->client->name }}
                                </a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>{{ $quote->issue_date->format('M d, Y') }}</td>
                        <td>
                            <span class="{{ $quote->valid_until->isPast() && $quote->status !== 'accepted' && $quote->status !== 'converted' ? 'text-danger fw-semibold' : '' }}">
                                {{ $quote->valid_until->format('M d, Y') }}
                            </span>
                        </td>
                        <td>
                            <span class="fw-semibold">{{ $currencySymbol }}{{ number_format($quote->total_amount, 2) }}</span>
                        </td>
                        <td>
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
                        </td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="{{ route('quotes.show', $quote->id) }}">
                                        <i class="fas fa-eye me-2"></i> View
                                    </a></li>
                                    <li><a class="dropdown-item" href="{{ route('quotes.pdf', $quote->id) }}">
                                        <i class="fas fa-file-pdf me-2"></i> Download PDF
                                    </a></li>
                                    <li><a class="dropdown-item" href="{{ route('quotes.edit', $quote->id) }}">
                                        <i class="fas fa-edit me-2"></i> Edit
                                    </a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('quotes.destroy', $quote->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Are you sure you want to delete this quote?')">
                                                <i class="fas fa-trash me-2"></i> Delete
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
        @else
        <div class="text-center py-5">
            <i class="fas fa-file-invoice fa-3x text-muted mb-3"></i>
            <h5>No quotes found</h5>
            <p class="text-muted mb-3">Create your first quote to get started</p>
            <a href="{{ route('quotes.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Create Quote
            </a>
        </div>
        @endif
    </div>
</div>

<!-- Pagination -->
@if($quotes->hasPages())
<div class="mt-4">
    {{ $quotes->links() }}
</div>
@endif
@endsection
