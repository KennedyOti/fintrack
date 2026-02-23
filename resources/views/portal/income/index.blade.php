@extends('layouts.portal')

@section('title', 'Income - FinTrack')

@section('content')
<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">Income</h4>
        <p class="text-muted mb-0">Track all your income sources</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('income.categories.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-tags me-1"></i> Categories
        </a>
        <a href="{{ route('income.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Add Income
        </a>
    </div>
</div>

<!-- Filters -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" placeholder="Search..." value="{{ $search }}">
            </div>
            <div class="col-md-2">
                <select name="category_id" class="form-select">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ $category_id == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="client_id" class="form-select">
                    <option value="">All Clients</option>
                    @foreach($clients as $client)
                    <option value="{{ $client->id }}" {{ $client_id == $client->id ? 'selected' : '' }}>
                        {{ $client->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" name="date_from" class="form-control" placeholder="From" value="{{ $date_from }}">
            </div>
            <div class="col-md-2">
                <input type="date" name="date_to" class="form-control" placeholder="To" value="{{ $date_to }}">
            </div>
            <div class="col-md-1">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Income Table -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        @if($incomes->count() > 0)
        <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Category</th>
                        <th>Client</th>
                        <th>Amount</th>
                        <th>Payment Method</th>
                        <th>Reference</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($incomes as $income)
                    <tr>
                        <td>{{ $income->income_date->format('M d, Y') }}</td>
                        <td>
                            @if($income->category)
                            <span class="badge bg-primary">{{ $income->category->name }}</span>
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>{{ $income->client ? $income->client->name : '-' }}</td>
                        <td class="fw-bold text-success">{{ number_format($income->amount, 2) }}</td>
                        <td>
                            @switch($income->payment_method)
                            @case('cash')
                            <span class="badge bg-info">Cash</span>
                            @break
                            @case('bank_transfer')
                            <span class="badge bg-primary">Bank Transfer</span>
                            @break
                            @case('mpesa')
                            <span class="badge bg-success">M-Pesa</span>
                            @break
                            @case('card')
                            <span class="badge bg-warning">Card</span>
                            @break
                            @default
                            <span class="badge bg-secondary">{{ ucfirst($income->payment_method) }}</span>
                            @endswitch
                        </td>
                        <td>{{ $income->reference_number ?: '-' }}</td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light" data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('income.show', $income->id) }}">
                                            <i class="fas fa-eye me-2"></i>View
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('income.edit', $income->id) }}">
                                            <i class="fas fa-edit me-2"></i>Edit
                                        </a>
                                    </li>
                                    <li>
                                        <form action="{{ route('income.destroy', $income->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger btn-delete">
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
            <i class="fas fa-money-bill-wave fa-3x text-muted mb-3"></i>
            <h5>No income records found</h5>
            <p class="text-muted">Start tracking your income by adding your first entry.</p>
            <a href="{{ route('income.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Add Income
            </a>
        </div>
        @endif
    </div>
</div>

<!-- Pagination -->
@if($incomes->hasPages())
<div class="mt-4">
    {{ $incomes->links() }}
</div>
@endif
@endsection
